<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentsResource\Pages;
use App\Filament\Resources\StudentsResource\RelationManagers\TutorsRelationManager;
use App\Models\Tutor;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StudentsResource extends Resource
{
    protected static ?string $model = Student::class;
    protected static ?string $navigationLabel = 'Estudiantes';
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationGroup = 'Gestión de padres y estudiantes';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\Section::make('Información del estudiante')
                            ->description('Ingrese los datos personales del estudiante')
                            ->columns(2)
                            ->schema([
                                Forms\Components\TextInput::make('first_name')
                                    ->label('Nombres')
                                    ->required()
                                    ->minLength(2)
                                    ->maxLength(100)
                                    ->placeholder('Ingrese los nombres'),

                                Forms\Components\TextInput::make('last_name')
                                    ->label('Apellidos')
                                    ->required()
                                    ->minLength(2)
                                    ->maxLength(100)
                                    ->placeholder('Ingrese los apellidos'),

                                Forms\Components\DatePicker::make('birth_date')
                                    ->label('Fecha de nacimiento')
                                    ->required()
                                    ->maxDate(now())
                                    ->format('Y-m-d'),

                                Forms\Components\Select::make('dni_type')
                                    ->label('Tipo de documento')
                                    ->options([
                                        'RC' => 'Registro Civil',
                                        'TI' => 'Tarjeta de Identidad',
                                        'CC' => 'Cédula de Ciudadanía',
                                    ])
                                    ->searchable()
                                    ->required()
                                    ->native(false)
                                    ->placeholder('Seleccione tipo de documento'),

                                Forms\Components\TextInput::make('dni')
                                    ->label('Número de documento')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(20)
                                    ->placeholder('Ingrese número de documento')
                                    ->disabledOn('edit'),
                            ]),

                        Forms\Components\Section::make('Padres o tutores')
                            ->description('Asigne los padres o tutores del estudiante')
                            ->schema([
                                Forms\Components\Select::make('tutors')
                                    ->label('Padres/Tutores')
                                    ->multiple()
                                    ->relationship(
                                        'tutors',
                                        'first_name',
                                        fn ($query) => $query->orderBy('first_name')
                                    )
                                    ->options(Tutor::query()
                                        ->orderBy('first_name')
                                        ->get()
                                        ->mapWithKeys(function ($tutor) {
                                            return [$tutor->id => $tutor->first_name . ' ' . $tutor->last_name . ' - ' . $tutor->dni];
                                        }))
                                    ->preload()
                                    ->required()
                                    ->createOptionForm([
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
                            ]),
                    ])->columnSpan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('first_name')
                    ->label('Nombres')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('last_name')
                    ->label('Apellidos')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('birth_date')
                    ->label('Fecha nacimiento')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('dni_type')
                    ->label('Tipo documento')
                    ->badge()
                    ->colors([
                        'warning' => 'TI',
                        'success' => 'RC',
                        'primary' => 'CC',
                    ]),

                Tables\Columns\TextColumn::make('dni')
                    ->label('Documento')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tutorss_count')
                    ->label('Padres/Tutores')
                    ->counts('tutors'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('dni_type')
                    ->label('Tipo de documento')
                    ->options([
                        'RC' => 'Registro Civil',
                        'TI' => 'Tarjeta de Identidad',
                        'CC' => 'Cédula de Ciudadanía',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Editar'),
                Tables\Actions\DeleteAction::make()
                    ->label('Eliminar'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            TutorsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudents::route('/create'),
            'edit' => Pages\EditStudents::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'first_name',
            'last_name',
            'dni',
        ];
    }

    public static function getGlobalSearchResultTitle(mixed $record): string
    {
        return $record->first_name . ' ' . $record->last_name;
    }
}
