<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TutorsResource\Pages;
use App\Models\City;
use App\Models\Country;
use App\Models\Tutor;
use App\Models\State;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Grid;

class TutorsResource extends Resource
{
    protected static ?string $model = Tutor::class;
    protected static ?string $navigationLabel = 'Padres de familia';
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationGroup = 'Gestión de padres y estudiantes';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()
                    ->schema([
                        Section::make('Información personal')
                            ->description('Ingrese los datos personales del padre/madre')
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

                                Forms\Components\TextInput::make('phone')
                                    ->label('Teléfono principal')
                                    ->required()
                                    ->tel()
                                    ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
                                    ->placeholder('+57 300 123 4567'),

                                Forms\Components\TextInput::make('secondary_phone')
                                    ->label('Teléfono secundario')
                                    ->tel()
                                    ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
                                    ->placeholder('+57 300 123 4567'),

                                Forms\Components\TextInput::make('email')
                                    ->label('Correo electrónico')
                                    ->email()
                                    ->required()
                                    ->placeholder('ejemplo@correo.com'),

                                Forms\Components\Select::make('dni_type')
                                    ->label('Tipo de documento')
                                    ->options([
                                        'CC' => 'Cédula de Ciudadanía',
                                        'TI' => 'Tarjeta de Identidad',
                                        'RC' => 'Registro Civil',
                                        'CE' => 'Cédula de Extranjería',
                                        'PA' => 'Pasaporte',
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

                                Forms\Components\Select::make('dni_expedition_country_id')
                                    ->label('País de expedición')
                                    ->options(Country::query()->orderBy('name')->pluck('name', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(fn (Set $set) => $set('dni_expedition_state_id', null))
                                    ->placeholder('Seleccione país'),

                                Forms\Components\Select::make('dni_expedition_state_id')
                                    ->label('Estado/Departamento de expedición')
                                    ->options(fn (Get $get): array => State::query()
                                        ->where('country_id', $get('dni_expedition_country_id'))
                                        ->orderBy('name')
                                        ->pluck('name', 'id')
                                        ->toArray()
                                    )
                                    ->searchable()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(fn (Set $set) => $set('dni_expedition_city', null))
                                    ->placeholder('Seleccione estado/departamento'),

                                Forms\Components\Select::make('dni_expedition_city')
                                    ->label('Ciudad de expedición')
                                    ->options(fn (Get $get): array => City::query()
                                        ->where('state_id', $get('dni_expedition_state_id'))
                                        ->orderBy('name')
                                        ->pluck('name', 'id')
                                        ->toArray()
                                    )
                                    ->searchable()
                                    ->required()
                                    ->placeholder('Seleccione ciudad'),
                            ]),

                        Section::make('Información de residencia')
                            ->description('Ingrese los datos de residencia actuales')
                            ->columns(2)
                            ->schema([
                                Forms\Components\TextInput::make('address')
                                    ->label('Dirección de residencia')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Ingrese dirección completa'),

                                Forms\Components\Select::make('residence_country_id')
                                    ->label('País de residencia')
                                    ->options(Country::query()->orderBy('name')->pluck('name', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(fn (Set $set) => $set('residence_state_id', null))
                                    ->placeholder('Seleccione país'),

                                Forms\Components\Select::make('residence_state_id')
                                    ->label('Estado/Departamento de residencia')
                                    ->options(fn (Get $get): array => State::query()
                                        ->where('country_id', $get('residence_country_id'))
                                        ->orderBy('name')
                                        ->pluck('name', 'id')
                                        ->toArray()
                                    )
                                    ->searchable()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(fn (Set $set) => $set('residence_city', null))
                                    ->placeholder('Seleccione estado/departamento'),

                                Forms\Components\Select::make('residence_city')
                                    ->label('Ciudad de residencia')
                                    ->options(fn (Get $get): array => City::query()
                                        ->where('state_id', $get('residence_state_id'))
                                        ->orderBy('name')
                                        ->pluck('name', 'id')
                                        ->toArray()
                                    )
                                    ->searchable()
                                    ->required()
                                    ->placeholder('Seleccione ciudad'),
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

                Tables\Columns\TextColumn::make('dni_type')
                    ->label('Tipo documento')
                    ->badge()
                    ->colors([
                        'primary' => 'CC',
                        'warning' => 'TI',
                        'success' => 'RC',
                        'danger' => 'CE',
                        'info' => 'PA',
                    ]),

                Tables\Columns\TextColumn::make('dni')
                    ->label('Documento')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Correo')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Teléfono')
                    ->searchable()
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('dni_type')
                    ->label('Tipo de documento')
                    ->options([
                        'CC' => 'Cédula de Ciudadanía',
                        'TI' => 'Tarjeta de Identidad',
                        'RC' => 'Registro Civil',
                        'CE' => 'Cédula de Extranjería',
                        'PA' => 'Pasaporte',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Editar'),
                Tables\Actions\Action::make('add_child')
                    ->label('Agregar Hijo')
                    ->icon('heroicon-o-plus')
                    ->color('success'),
//                    ->url(fn (Parents $record): string => route('filament.admin.resources.students.create', ['parent_id' => $record->id])),
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
            //RelationManagers\StudentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTutors::route('/'),
            'create' => Pages\CreateTutors::route('/create'),
            'edit' => Pages\EditTutors::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'first_name',
            'last_name',
            'email',
            'dni',
            'phone',
        ];
    }

    public static function getGlobalSearchResultTitle(mixed $record): string
    {
        return $record->first_name . ' ' . $record->last_name;
    }
}
