<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StaffResource\Pages;
use App\Models\Instructor;
use Filament\Forms;
use Filament\Forms\Form as FilamentForm;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table as FilamentTable;
use Illuminate\Database\Eloquent\Builder;

class StaffResource extends Resource
{
    protected static ?string $model = Instructor::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Usuários';

    public static function getNavigationLabel(): string
    {
        return 'Professores';
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
                            'staff' => 'Professor',
                            'admin' => 'Administrador',
                            'student' => 'Aluno',
                        ])
                        ->default(['staff'])
                        ->required()
                        ->columns(3)
                        ->label('Perfis')
                        ->visible(fn (): bool => auth()->user()->isAdmin()),
                    Forms\Components\TextInput::make('password')
                        ->password()
                        ->required(fn (string $context): bool => $context === 'create')
                        ->minLength(8)
                        ->dehydrated(fn ($state) => filled($state))
                        ->revealable()
                        ->label('Senha'),
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
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->label('Email'),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->sortable()
                    ->label('Telefone'),
                Tables\Columns\BadgeColumn::make('role')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'staff' => 'Professor',
                        'admin' => 'Administrador',
                        default => ucfirst($state),
                    })
                    ->colors([
                        'warning' => 'staff',
                        'danger' => 'admin',
                    ])
                    ->label('Perfil'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Criado em'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->options([
                        'staff' => 'Professor',
                        'admin' => 'Administrador',
                    ])
                    ->label('Perfil'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('id', '!=', auth()->id()); // Exclude current user
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStaff::route('/'),
            'create' => Pages\CreateStaff::route('/create'),
            'edit' => Pages\EditStaff::route('/{record}/edit'),
        ];
    }
}
