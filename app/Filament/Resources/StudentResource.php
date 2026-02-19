<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Models\Student;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Forms\Form as FilamentForm;
use Filament\Tables\Table as FilamentTable;
use Illuminate\Database\Eloquent\Builder;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $modelLabel = 'Aluno';

    protected static ?string $pluralModelLabel = 'Alunos';

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Usuários';

    public static function getNavigationLabel(): string
    {
        return 'Alunos';
    }

    public static function form(FilamentForm $form): FilamentForm
    {
        return $form->schema([
            Forms\Components\Section::make('Informações Pessoais')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->label('Nome'),
                    Forms\Components\TextInput::make('phone')
                        ->required()
                        ->maxLength(40)
                        ->tel()
                        ->mask('(99) 99999-9999')
                        ->placeholder('(21) 96447-0631')
                        ->label('Telefone'),
                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true)
                        ->label('Email'),
                ])->columns(2),

            Forms\Components\Section::make('Configurações da Conta')
                ->schema([
                    Forms\Components\CheckboxList::make('role')
                        ->options([
                            'student' => 'Aluno',
                            'staff' => 'Professor',
                            'admin' => 'Administrador',
                        ])
                        ->default(['student'])
                        ->required()
                        ->columns(3)
                        ->label('Perfis')
                        ->visible(fn (): bool => auth()->user()->isAdmin()),
                    Forms\Components\Toggle::make('email_verified_at')
                        ->label('Email Verificado')
                        ->default(true)
                        ->dehydrated(false)
                        ->afterStateHydrated(function ($component, $state) {
                            $component->state(!is_null($state));
                        })
                        ->dehydrateStateUsing(fn ($state) => $state ? now() : null),
                ]),
        ]);
    }

    public static function table(FilamentTable $table): FilamentTable
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Nome'),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->toggleable()
                    ->label('Telefone')
                    ->formatStateUsing(fn ($state) => \App\Helpers\PhoneNormalizer::formatForDisplay($state)),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->toggleable()
                    ->label('Email'),
                Tables\Columns\TextColumn::make('role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'staff' => 'warning',
                        'student' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'admin' => 'Administrador',
                        'staff' => 'Professor',
                        'student' => 'Aluno',
                        default => ucfirst($state),
                    })
                    ->sortable()
                    ->toggleable()
                    ->label('Perfil'),
                Tables\Columns\TextColumn::make('enrollments_count')
                    ->counts('enrollments')
                    ->label('Matrículas')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Criado em'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->options([
                        'student' => 'Aluno',
                        'staff' => 'Professor',
                        'admin' => 'Administrador',
                    ])
                    ->label('Perfil')
                    ->visible(fn (): bool => auth()->user()->isAdmin()),
                Tables\Filters\TernaryFilter::make('email_verified_at')
                    ->label('Email Verificado')
                    ->nullable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn (): bool => auth()->user()->isAdmin()),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->visible(fn (): bool => auth()->user()->isAdmin()),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            // Admin sees all users with student role or enrolled students
            return parent::getEloquentQuery()
                ->where(function ($query) {
                    $query->whereJsonContains('role', 'student')
                        ->orWhereHas('enrollments');
                });
        }
        
        if ($user->isStaff()) {
            // Staff sees students who have at least one check-in in a class taught by this instructor
            // OR students currently enrolled in modalities the instructor teaches
            return parent::getEloquentQuery()
                ->whereJsonContains('role', 'student')
                ->whereHas('enrollments', function ($q) use ($user) {
                    $q->whereHas('gymClass', function ($q2) use ($user) {
                        $q2->where('instructor_id', $user->id);
                    });
                });
        }
        
        // Default: only students
        return parent::getEloquentQuery()->whereJsonContains('role', 'student');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            // 'create' => Pages\CreateStudent::route('/create'), // Students should be created via invites, not directly
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}
