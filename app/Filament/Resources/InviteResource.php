<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InviteResource\Pages;
use App\Models\Invite;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Forms\Form as FilamentForm;
use Filament\Tables\Table as FilamentTable;

class InviteResource extends Resource
{
    protected static ?string $model = Invite::class;

    protected static ?string $modelLabel = 'Convite';

    protected static ?string $pluralModelLabel = 'Convites';

    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';

    protected static ?string $navigationGroup = 'Usuários';

    public static function getNavigationLabel(): string
    {
        return 'Convites';
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['phone'] = \App\Helpers\PhoneNormalizer::normalize($data['phone'] ?? '');
        return $data;
    }

    public static function mutateFormDataBeforeSave(array $data): array
    {
        $data['phone'] = \App\Helpers\PhoneNormalizer::normalize($data['phone'] ?? '');
        return $data;
    }

    public static function form(FilamentForm $form): FilamentForm
    {
        return $form->schema([
            Forms\Components\Section::make('Detalhes do Convite')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nome do Destinatário')
                        ->required()
                        ->maxLength(255)
                        ->helperText('Nome da pessoa que está sendo convidada'),
                    Forms\Components\TextInput::make('phone')
                        ->required()
                        ->maxLength(40)
                        ->tel()
                        ->mask('(99) 99999-9999')
                        ->placeholder('(21) 96447-0631')
                        ->helperText('Formato brasileiro: (xx) 9xxxx-xxxx'),
                    Forms\Components\Select::make('role')
                        ->label('Convite para')
                        ->options([
                            'student' => 'Aluno',
                            'staff' => 'Professor',
                            'admin' => 'Administrador',
                        ])
                        ->default('student')
                        ->required()
                        ->helperText('Selecione o Perfil do usuário convidado'),
                    Forms\Components\DatePicker::make('expires_at')
                        ->label('Data de Expiração')
                        ->helperText('Deixe vazio para não expirar'),
                ])->columns(2),
        ]);
    }

    public static function table(FilamentTable $table): FilamentTable
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Nome')
                    ->placeholder('Sem nome'),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->sortable()
                    ->label('Telefone')
                    ->formatStateUsing(fn ($state) => \App\Helpers\PhoneNormalizer::formatForDisplay($state)),
                Tables\Columns\BadgeColumn::make('role')
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'student' => 'Aluno',
                        'staff' => 'Professor',
                        'admin' => 'Administrador',
                        default => ucfirst($state),
                    })
                    ->colors([
                        'success' => 'student',
                        'warning' => 'staff',
                        'danger' => 'admin',
                    ])
                    ->sortable()
                    ->label('Perfil'),
                Tables\Columns\TextColumn::make('expires_at')
                    ->date(fn ($state) => $state ? $state->format('d/m/Y') : 'Nunca')
                    ->sortable()
                    ->label('Expira em')
                    ->placeholder('Nunca'),
                Tables\Columns\BadgeColumn::make('used_at')
                    ->label('Status')
                    ->formatStateUsing(fn ($state) => $state ? 'Usado' : 'Pendente')
                    ->colors([
                        'success' => fn ($state) => !$state,
                        'danger' => fn ($state) => (bool) $state,
                    ])
                    ->sortable(),
                Tables\Columns\TextColumn::make('invite_link')
                    ->label('Link do Convite')
                    ->getStateUsing(function ($record) {
                        $token = $record->token;
                        if (strlen($token) <= 8) {
                            return $token;
                        }
                        return substr($token, 0, 8) . '...';
                    })
                    ->copyable()
                    ->copyableState(fn ($record) => route('invite.show', $record->token))
                    ->color(function ($record) {
                        if ($record->used_at) {
                            return 'gray';
                        }
                        if ($record->expires_at && $record->expires_at->isPast()) {
                            return 'danger';
                        }
                        return 'info';
                    })
                    ->placeholder('N/A')
                    ->tooltip('Clique para copiar o link completo'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Criado em')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('used_at')
                    ->label('Status')
                    ->nullable()
                    ->placeholder('Todos')
                    ->trueLabel('Usados')
                    ->falseLabel('Pendentes'),
                Tables\Filters\SelectFilter::make('role')
                    ->options([
                        'student' => 'Aluno',
                        'staff' => 'Professor',
                        'admin' => 'Administrador',
                    ])
                    ->label('Perfil'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Editar'),
                Tables\Actions\DeleteAction::make()
                    ->label('Excluir'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->label('Excluir selecionados'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Novo Convite'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvites::route('/'),
            'create' => Pages\CreateInvite::route('/create'),
            'edit' => Pages\EditInvite::route('/{record}/edit'),
        ];
    }

}
