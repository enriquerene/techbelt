<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ModalityResource\Pages;
use App\Models\Modality;
use App\Models\Enrollment;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Forms\Form as FilamentForm;
use Filament\Tables\Table as FilamentTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ModalityResource extends Resource
{
    protected static ?string $model = Modality::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    public static ?string $title = 'Modalidade';

    public static function getNavigationLabel(): string
    {
        return 'Modalidades';
    }

    public static function form(FilamentForm $form): FilamentForm
    {
        return $form->schema([
            Forms\Components\Section::make('Informações Básicas')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->label('Nome')
                        ->afterStateUpdated(function ($state, $set) {
                            $set('slug', \Illuminate\Support\Str::slug($state));
                        }),
                    Forms\Components\TextInput::make('slug')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true)
                        ->label('Slug')
                        ->helperText('Gerado automaticamente a partir do nome, mas pode ser personalizado'),
                    Forms\Components\Textarea::make('description')
                        ->rows(3)
                        ->columnSpanFull()
                        ->label('Descrição'),
                    Forms\Components\FileUpload::make('image')
                        ->label('Imagem da Modalidade')
                        ->image()
                        ->directory('modalities')
                        ->maxSize(2048)
                        ->nullable()
                        ->helperText('Imagem opcional para esta modalidade (máx. 2MB)'),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Ativa')
                        ->default(true)
                        ->required(),
                    Forms\Components\TextInput::make('order')
                        ->numeric()
                        ->default(0)
                        ->required()
                        ->label('Ordem'),
                ])->columns(2),
        ]);
    }

    public static function table(FilamentTable $table): FilamentTable
    {
        return $table
            ->reorderable('order')
            ->defaultSort('order')
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Imagem')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&color=FFFFFF&background=3b82f6')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Nome'),
                Tables\Columns\TextColumn::make('classes_count')
                    ->counts('classes')
                    ->label('Turmas')
                    ->sortable(),
                Tables\Columns\TextColumn::make('students_count')
                    ->label('Alunos')
                    ->sortable()
                    ->numeric(),
                Tables\Columns\TextColumn::make('order')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Ordem'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Criado em'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Ativo')
                    ->trueLabel('Somente ativas')
                    ->falseLabel('Somente inativas'),
                    // ->nullableLabel('Todas'),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->hidden(fn (Modality $record) => $record->classes()->exists()),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])
            ->withCount(['classes'])
            ->addSelect([
                'students_count' => Enrollment::selectRaw('COUNT(DISTINCT user_id)')
                    ->join('classes', 'enrollments.class_id', '=', 'classes.id')
                    ->whereColumn('classes.modality_id', 'modalities.id')
                    ->limit(1)
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListModalities::route('/'),
            'create' => Pages\CreateModality::route('/create'),
            'edit' => Pages\EditModality::route('/{record}/edit'),
        ];
    }
}
