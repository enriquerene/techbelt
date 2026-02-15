<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GymClassResource\Pages;
use App\Models\GymClass;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Forms\Form as FilamentForm;
use Filament\Tables\Table as FilamentTable;

class GymClassResource extends Resource
{
    protected static ?string $model = GymClass::class;

    protected static ?string $modelLabel = 'Turma';

    protected static ?string $pluralModelLabel = 'Turmas';

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    public static function getNavigationLabel(): string
    {
        return 'Turmas';
    }

    public static function form(FilamentForm $form): FilamentForm
    {
        return $form->schema([
            Forms\Components\Section::make('Informação Básica')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->label('Nome da Turma'),
                    Forms\Components\Select::make('modality_id')
                        ->relationship('modality', 'name')
                        ->required()
                        ->label('Modalidade')
                        ->searchable()
                        ->preload(),
                    Forms\Components\Select::make('instructor_id')
                        ->relationship('instructor','name')
                        ->required()
                        ->label('Professor')
                        ->searchable()
                        ->preload()
                        ->helperText('Selecione o professor para esta turma'),
                    Forms\Components\TextInput::make('capacity')
                        ->numeric()
                        ->minValue(1)
                        ->default(20)
                        ->label('Capacidade Máxima'),
                ])->columns(2),

            Forms\Components\Section::make('Horários e Notas')
                ->schema([
                    Forms\Components\CheckboxList::make('schedule_days')
                        ->label('Dias da Semana')
                        ->options([
                            'mon' => 'Segunda-feira',
                            'tue' => 'Terça-feira',
                            'wed' => 'Quarta-feira',
                            'thu' => 'Quinta-feira',
                            'fri' => 'Sexta-feira',
                            'sat' => 'Sábado',
                        ])
                        ->columns(3)
                        ->required()
                        ->helperText('Selecione os dias em que esta turma ocorre')
                        ->dehydrated(false)
                        ->afterStateHydrated(function ($component, $state, $record) {
                            if ($record && $record->schedule) {
                                $schedule = is_array($record->schedule) ? $record->schedule : json_decode($record->schedule, true);
                                $component->state($schedule['days'] ?? []);
                            }
                        }),
                    Forms\Components\TimePicker::make('schedule_time')
                        ->label('Horário da Aula')
                        ->required()
                        ->seconds(false)
                        ->format('H:i')
                        ->displayFormat('H:i')
                        ->extraAttributes(['step' => 60])
                        ->helperText('Horário em que a aula começa')
                        ->dehydrated(false)
                        ->afterStateHydrated(function ($component, $state, $record) {
                            if ($record && $record->schedule) {
                                $schedule = is_array($record->schedule) ? $record->schedule : json_decode($record->schedule, true);
                                // Garante que o valor inicial seja algo como '19:00' e não '07:00 PM'
                                $time = $schedule['time'] ?? '19:00';
                                $component->state(date('H:i', strtotime($time)));
                            }
                        }),
                    Forms\Components\Hidden::make('schedule')
                        ->dehydrateStateUsing(function ($get) {
                            return json_encode([
                                'days' => $get('schedule_days') ?? [],
                                'time' => $get('schedule_time'),
                            ]);
                        }),
                    Forms\Components\Textarea::make('notes')
                        ->rows(3)
                        ->label('Notas Adicionais')
                        ->placeholder('Qualquer informação adicional sobre esta turma'),
                ]),
        ]);
    }

    public static function table(FilamentTable $table): FilamentTable
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->searchable()->sortable()->label('Nome'),
            Tables\Columns\TextColumn::make('modality.name')->label('Modalidade')->sortable(),
            Tables\Columns\TextColumn::make('instructor.name')->label('Professor')->sortable(),
            Tables\Columns\TextColumn::make('capacity')->sortable()->label('Capacidade'),
            Tables\Columns\TextColumn::make('schedule')
                ->label('Horário')
                ->formatStateUsing(function ($state) {
                    if (empty($state)) {
                        return 'Sem horário';
                    }
                    
                    $schedule = is_array($state) ? $state : json_decode($state, true);
                    $days = $schedule['days'] ?? [];
                    $time = $schedule['time'] ?? '';
                    
                    if (empty($days) || empty($time)) {
                        return 'Sem horário';
                    }
                    
                    $dayNames = [
                        'mon' => 'Seg',
                        'tue' => 'Ter',
                        'wed' => 'Qua',
                        'thu' => 'Qui',
                        'fri' => 'Sex',
                        'sat' => 'Sáb',
                    ];
                    
                    $formattedDays = array_map(function ($day) use ($dayNames) {
                        return $dayNames[$day] ?? $day;
                    }, $days);
                    
                    return implode(', ', $formattedDays) . ' às ' . $time;
                })
                ->sortable()
                ->searchable(),
        ])->actions([
            Tables\Actions\EditAction::make(),
        ])->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
        ])->headerActions([
            Tables\Actions\CreateAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGymClasses::route('/'),
            'create' => Pages\CreateGymClass::route('/create'),
            'edit' => Pages\EditGymClass::route('/{record}/edit'),
        ];
    }
}
