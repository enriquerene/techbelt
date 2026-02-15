<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EnrollmentResource\Pages;
use App\Models\Enrollment;
use App\Models\PricingTier;
use App\Models\Payment;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Forms\Form as FilamentForm;
use Filament\Tables\Table as FilamentTable;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Components\Tab;
use Illuminate\Validation\Rule;
use Illuminate\Support\HtmlString;

use function Pest\Laravel\json;

class EnrollmentResource extends Resource
{
    protected static ?string $model = Enrollment::class;

    protected static ?string $modelLabel = 'Matrícula';

    protected static ?string $pluralModelLabel = 'Matrículas';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function getNavigationLabel(): string
    {
        return 'Matrículas';
    }

    public static function form(FilamentForm $form): FilamentForm
    {
        return $form->schema([
            Forms\Components\Section::make('Detalhes do Contrato')
                ->schema([
                    Forms\Components\Select::make('user_id')
                        ->relationship('user', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->label('Aluno'),
                    Forms\Components\Select::make('pricing_tier_id')
                        ->relationship('pricingTier', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->label('Plano')
                        ->live()
                        ->afterStateUpdated(function ($state, Forms\Set $set) {
                            if ($state) {
                                $pricingTier = PricingTier::find($state);
                                if ($pricingTier) {
                                    $set('amount', null); // Reset amount when plan changes
                                    $set('is_custom_price', false);
                                }
                            }
                        }),
                    Forms\Components\TextInput::make('amount')
                        ->numeric()
                        ->prefix('R$')
                        ->label('Valor Personalizado')
                        ->helperText(function (Forms\Get $get) {
                            $tierId = $get('pricing_tier_id');
                            if (!$tierId) {
                                return 'Selecione um plano primeiro para ver o valor padrão';
                            }
                            
                            $tier = PricingTier::find($tierId);
                            if (!$tier) {
                                return 'Plano não encontrado';
                            }
                            
                            return new HtmlString(
                                'Valor padrão do plano: <strong>R$ ' . number_format($tier->price, 2, ',', '.') . '</strong>. ' .
                                'Deixe em branco para usar o valor padrão.'
                            );
                        })
                        ->nullable()
                        ->minValue(0)
                        ->step(0.01),
                    Forms\Components\Toggle::make('is_custom_price')
                        ->label('Usar valor personalizado?')
                        ->default(false)
                        ->live()
                        ->helperText('Ative para definir um valor diferente do plano')
                        ->hidden(fn (Forms\Get $get) => is_null($get('amount'))),
                ])->columns(2),

            Forms\Components\Section::make('Atribuição de Turmas')
                ->schema([
                    Forms\Components\Select::make('pricing_tier_id')
                        ->relationship('pricingTier', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->label('Plano')
                        ->live()
                        ->disabled() // Already selected above, but we need it for the helper
                        ->dehydrated(false),
                    Forms\Components\Select::make('classes')
                        ->relationship('classes', 'name')
                        ->searchable()
                        ->preload()
                        ->multiple()
                        ->label('Turmas')
                        ->helperText(function (Forms\Get $get) {
                            $tierId = $get('pricing_tier_id');
                            if (!$tierId) {
                                return 'Selecione um plano primeiro';
                            }
                            
                            $tier = PricingTier::find($tierId);
                            if (!$tier) {
                                return 'Plano não encontrado';
                            }
                            
                            $selectedCount = count($get('classes') ?? []);
                            $remaining = max(0, $tier->class_count - $selectedCount);
                            
                            return new HtmlString(
                                "Plano <strong>{$tier->name}</strong> permite <strong>{$tier->class_count}</strong> turmas.<br>" .
                                "Selecionadas: <strong>{$selectedCount}</strong>. Restantes: <strong>{$remaining}</strong>."
                            );
                        })
                        ->rules([
                            'array',
                            function ($get) {
                                $tierId = $get('pricing_tier_id');
                                if (!$tierId) {
                                    return;
                                }
                                
                                $tier = PricingTier::find($tierId);
                                if (!$tier) {
                                    return;
                                }
                                
                                return Rule::requiredIf(fn() => $tier->class_count > 0);
                            },
                            function ($get) {
                                return function ($attribute, $value, $fail) use ($get) {
                                    $tierId = $get('pricing_tier_id');
                                    if (!$tierId) {
                                        return;
                                    }
                                    
                                    $tier = PricingTier::find($tierId);
                                    if (!$tier) {
                                        return;
                                    }
                                    
                                    $selectedCount = count($value ?? []);
                                    if ($selectedCount > $tier->class_count) {
                                        $fail("O plano {$tier->name} permite apenas {$tier->class_count} turmas. Você selecionou {$selectedCount}.");
                                    }
                                };
                            },
                        ])
                        ->columns(2),
                ]),

            Forms\Components\Section::make('Datas')
                ->schema([
                    Forms\Components\DateTimePicker::make('enrolled_at')
                        ->default(now())
                        ->seconds(false)
                        ->displayFormat("d/m/Y H:i")
                        ->label('Data de Matrícula'),
                    Forms\Components\DateTimePicker::make('next_billing_date')
                        ->default(now()->addMonth())
                        ->seconds(false)
                        ->displayFormat("d/m/Y H:i")
                        ->label('Próxima Data de Cobrança'),
                ])->columns(2),

            Forms\Components\Section::make('Status')
                ->schema([
                    Forms\Components\Select::make('status')
                        ->options([
                            'active' => 'Ativa',
                            'pending' => 'Pendente',
                            'overdue' => 'Atrasada',
                            'cancelled' => 'Cancelada',
                        ])
                        ->default('active')
                        ->required()
                        ->label('Status'),
                    Forms\Components\Textarea::make('notes')
                        ->rows(3)
                        ->columnSpanFull()
                        ->label('Observações'),
                ]),
        ]);
    }

    public static function table(FilamentTable $table): FilamentTable
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Aluno')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pricingTier.name')
                    ->label('Plano')
                    ->sortable(),
                Tables\Columns\TextColumn::make('final_price')
                    ->money('BRL')
                    ->sortable()
                    ->label('Valor')
                    ->description(function (Enrollment $record) {
                        if ($record->is_custom_price) {
                            return 'Personalizado';
                        }
                        return 'Padrão do plano';
                    }),
                Tables\Columns\TextColumn::make('classes_count')
                    ->label('Turmas')
                    ->counts('classes')
                    ->sortable()
                    ->description(function (Enrollment $record) {
                        if ($record->pricingTier) {
                            return "{$record->classes()->count()}/{$record->pricingTier->class_count}";
                        }
                        return null;
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'overdue' => 'danger',
                        'cancelled' => 'gray',
                        default => 'warning',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Ativa',
                        'pending' => 'Pendente',
                        'overdue' => 'Atrasada',
                        'cancelled' => 'Cancelada',
                        default => $state,
                    })
                    ->sortable()
                    ->label('Status'),
                Tables\Columns\TextColumn::make('next_billing_date')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Próxima Cobrança'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Criado em'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Ativa',
                        'pending' => 'Pendente',
                        'overdue' => 'Atrasada',
                        'cancelled' => 'Cancelada',
                    ])
                    ->label('Status'),
                Tables\Filters\SelectFilter::make('pricing_tier_id')
                    ->relationship('pricingTier', 'name')
                    ->label('Plano'),
                Tables\Filters\TernaryFilter::make('is_custom_price')
                    ->label('Valor Personalizado')
                    ->trueLabel('Sim')
                    ->falseLabel('Não')
                    ->queries(
                        true: fn ($query) => $query->where('is_custom_price', true),
                        false: fn ($query) => $query->where('is_custom_price', false),
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('cancel')
                    ->label('Cancelar')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->requiresConfirmation()
                    ->modalHeading('Cancelar Matrícula')
                    ->modalDescription('Esta ação cancelará a matrícula. O aluno não terá mais acesso às turmas.')
                    ->form([
                        Forms\Components\Select::make('cancellation_reason')
                            ->options([
                                'user_request' => 'Solicitação do Aluno',
                                'medical' => 'Motivo Médico/Lesão',
                                'payment_issue' => 'Inadimplência',
                                'other' => 'Outro',
                            ])
                            ->required()
                            ->label('Motivo do Cancelamento'),
                        Forms\Components\Textarea::make('notes')
                            ->label('Observações Adicionais')
                            ->rows(3),
                    ])
                    ->action(function (Enrollment $record, array $data) {
                        $record->update([
                            'status' => 'cancelled',
                            'cancellation_reason' => $data['cancellation_reason'],
                            'cancelled_at' => now(),
                            'notes' => $record->notes . "\nCancelada: " . $data['cancellation_reason'] . "\n" . ($data['notes'] ?? ''),
                        ]);

                        Notification::make()
                            ->title('Matrícula Cancelada')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Enrollment $record) => $record->isActive()),

                Tables\Actions\Action::make('renew')
                    ->label('Renovar')
                    ->icon('heroicon-o-arrow-path')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('Renovação Manual')
                    ->modalDescription('Estenda a data da próxima cobrança pelo período de faturamento do plano.')
                    ->action(function (Enrollment $record) {
                        $record->update([
                            'next_billing_date' => $record->next_billing_date->addMonth(),
                            'notes' => $record->notes . "\nRenovada manualmente em " . now()->format('d/m/Y'),
                        ]);

                        Notification::make()
                            ->title('Matrícula Renovada')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Enrollment $record) => $record->isActive()),

                Tables\Actions\Action::make('addPayment')
                    ->label('Registrar Pagamento')
                    ->icon('heroicon-o-credit-card')
                    ->color('success')
                    ->form([
                        Forms\Components\Select::make('payment_method')
                            ->options(Payment::paymentMethodOptions())
                            ->required()
                            ->label('Método de Pagamento'),
                        Forms\Components\TextInput::make('amount')
                            ->numeric()
                            ->prefix('R$')
                            ->required()
                            ->label('Valor')
                            ->default(fn (Enrollment $record) => $record->final_price),
                        Forms\Components\Select::make('status')
                            ->options(Payment::statusOptions())
                            ->default(Payment::STATUS_COMPLETED)
                            ->required()
                            ->label('Status'),
                        Forms\Components\Textarea::make('notes')
                            ->label('Observações')
                            ->rows(3),
                    ])
                    ->action(function (Enrollment $record, array $data) {
                        $payment = $record->payments()->create([
                            'amount' => $data['amount'],
                            'payment_method' => $data['payment_method'],
                            'status' => $data['status'],
                            'notes' => $data['notes'],
                            'paid_at' => $data['status'] === Payment::STATUS_COMPLETED ? now() : null,
                        ]);

                        Notification::make()
                            ->title('Pagamento Registrado')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Enrollment $record) => $record->isActive()),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export')
                    ->label('Exportar')
                    ->icon('heroicon-o-document-arrow-up')
                    ->color('primary')
                    ->action(function () {
                        // Implement export logic here (e.g., generate CSV or Excel file)
                        Notification::make()
                            ->title('Matrículas Exportadas')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('create')
                    ->label('Nova Matrícula')
                    ->icon('heroicon-o-plus')
                    ->color('success')
                    ->url(fn () => Pages\CreateEnrollment::getUrl()),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informações do Aluno')
                    ->schema([
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('Nome do Aluno'),
                        Infolists\Components\TextEntry::make('user.phone')
                            ->label('Telefone'),
                        Infolists\Components\TextEntry::make('user.email')
                            ->label('E-mail'),
                    ])->columns(3),

                Infolists\Components\Section::make('Detalhes do Contrato')
                    ->schema([
                        Infolists\Components\TextEntry::make('pricingTier.name')
                            ->label('Plano'),
                        Infolists\Components\TextEntry::make('final_price')
                            ->money('BRL')
                            ->label('Valor')
                            ->helperText(function (Enrollment $record) {
                                if ($record->is_custom_price) {
                                    return 'Valor personalizado (padrão: R$ ' . number_format($record->pricingTier->price, 2, ',', '.') . ')';
                                }
                                return 'Valor padrão do plano';
                            }),
                        Infolists\Components\TextEntry::make('enrolled_at')
                            ->dateTime()
                            ->label('Data de Matrícula'),
                    ])->columns(3),

                Infolists\Components\Section::make('Turmas Matriculadas')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('classes')
                            ->schema([
                                Infolists\Components\TextEntry::make('name')
                                    ->label('Turma'),
                                Infolists\Components\TextEntry::make('modality.name')
                                    ->label('Modalidade'),
                                Infolists\Components\TextEntry::make('instructor')
                                    ->label('Professor')
                                    ->formatStateUsing(function ($record) {
                                        $data = json_decode($record, true);
                                        return $data['instructor']['name'];
                                    }),
                                Infolists\Components\TextEntry::make('schedule')
                                    ->label('Horário')
                                    ->formatStateUsing(function($record) {
                                        $schedule = json_decode($record->schedule, true);
                                        $dayName = [
                                            'mon' => 'Segunda',
                                            'tue' => 'Terça',
                                            'wed' => 'Quarta',
                                            'thu' => 'Quinta',
                                            'fri' => 'Sexta',
                                            'sat' => 'Sábado',
                                        ];
                                        $time = $schedule['time'];
                                        $days = array_map(fn ($d) => $dayName[$d], $schedule['days']);
                                        return implode(', ', $days) . ' - ' . $time;
                                    }),
                            ])
                            ->columns(4)
                            ->label('')
                            ->hidden(fn (Enrollment $record) => $record->classes->isEmpty()),
                        Infolists\Components\TextEntry::make('classes_summary')
                            ->label('Resumo')
                            ->state(function (Enrollment $record) {
                                $count = $record->classes()->count();
                                $limit = $record->pricingTier?->class_count ?? 0;
                                return "{$count}/{$limit} turmas utilizadas";
                            })
                            ->hidden(fn (Enrollment $record) => $record->classes->isNotEmpty()),
                    ]),

                Infolists\Components\Section::make('Informações de Cobrança')
                    ->schema([
                        Infolists\Components\TextEntry::make('next_billing_date')
                            ->dateTime()
                            ->label('Próxima Data de Cobrança'),
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'active' => 'success',
                                'overdue' => 'danger',
                                'cancelled' => 'gray',
                                default => 'warning',
                            })
                            ->label('Status'),
                    ])->columns(2),

                Infolists\Components\Section::make('Histórico de Pagamentos')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('payments')
                            ->schema([
                                Infolists\Components\TextEntry::make('amount')
                                    ->money('BRL')
                                    ->label('Valor'),
                                Infolists\Components\TextEntry::make('payment_method')
                                    ->label('Método')
                                    ->formatStateUsing(fn (string $state): string => Payment::paymentMethodOptions()[$state] ?? $state),
                                Infolists\Components\TextEntry::make('status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        Payment::STATUS_COMPLETED => 'success',
                                        Payment::STATUS_PENDING => 'warning',
                                        Payment::STATUS_FAILED => 'danger',
                                        Payment::STATUS_REFUNDED => 'gray',
                                        default => 'gray',
                                    })
                                    ->label('Status'),
                                Infolists\Components\TextEntry::make('paid_at')
                                    ->dateTime()
                                    ->label('Data do Pagamento'),
                            ])
                            ->columns(4)
                            ->label('')
                            ->hidden(fn (Enrollment $record) => $record->payments->isEmpty()),
                        Infolists\Components\TextEntry::make('no_payments')
                            ->label('')
                            ->state('Nenhum pagamento registrado')
                            ->hidden(fn (Enrollment $record) => $record->payments->isNotEmpty()),
                    ]),

                Infolists\Components\Section::make('Observações')
                    ->schema([
                        Infolists\Components\TextEntry::make('notes')
                            ->columnSpanFull()
                            ->markdown(),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEnrollments::route('/'),
            'create' => Pages\CreateEnrollment::route('/create'),
            'view' => Pages\ViewEnrollment::route('/{record}'),
            // Edit page removed for strict "no-edit" strategy
            // 'edit' => Pages\EditEnrollment::route('/{record}/edit'),
        ];
    }
}
