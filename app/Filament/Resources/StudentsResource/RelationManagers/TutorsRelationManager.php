<?php

namespace App\Filament\Resources\StudentsResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class TutorsRelationManager extends RelationManager
{
    protected static string $relationship = 'tutors';
    protected static ?string $title = 'Padres/Tutores';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('relationship')
                    ->label('Parentesco')
                    ->options([
                        'father' => 'Padre',
                        'mother' => 'Madre',
                        'guardian' => 'Tutor Legal',
                    ])
                    ->required(),

                Forms\Components\Toggle::make('is_primary_contact')
                    ->label('Contacto principal')
                    ->default(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('first_name')
            ->columns([
                Tables\Columns\TextColumn::make('first_name')
                    ->label('Nombres'),

                Tables\Columns\TextColumn::make('last_name')
                    ->label('Apellidos'),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Teléfono'),

                Tables\Columns\TextColumn::make('pivot.relationship')
                    ->label('Parentesco'),

                Tables\Columns\IconColumn::make('pivot.is_primary_contact')
                    ->label('Contacto principal')
                    ->boolean(),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect()
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect()
                            ->label('Padre/Madre/Tutor')
                            ->required(),
                        Forms\Components\Select::make('relationship')
                            ->label('Parentesco')
                            ->options([
                                'father' => 'Padre',
                                'mother' => 'Madre',
                                'guardian' => 'Tutor Legal',
                            ])
                            ->required(),
                        Forms\Components\Toggle::make('is_primary_contact')
                            ->label('Contacto principal')
                            ->default(false),
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
