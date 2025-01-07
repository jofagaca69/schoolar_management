<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EnrollmentResource\Pages;
use App\Models\Enrollment;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EnrollmentResource extends Resource
{
    protected static ?string $model = Enrollment::class;
    protected static ?string $navigationLabel = 'Matrículas';
    protected static ?string $navigationGroup = 'Gestión académica';



    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('student_id')
                    ->relationship(
                        'student',
                        'first_name',
                        fn ($query) => $query->orderBy('first_name')
                    )
                    ->options(Student::query()
                        ->orderBy('first_name')
                        ->get()
                        ->mapWithKeys(function ($student) {
                            return [$student->id => $student->first_name . ' ' . $student->last_name . ' - ' . $student->dni];
                        }))
                    ->searchable(['first_name', 'last_name'])
                    ->preload()
                    ->required(),

                Forms\Components\Select::make('grade_id')
                    ->relationship(name: 'grade', titleAttribute: 'name')
                    ->required(),

                Forms\Components\Select::make('academic_year_id')
                    ->relationship(name: 'academicYear', titleAttribute: 'year')
                    ->required(),

                Forms\Components\DatePicker::make('enrollment_date')
                    ->required()
                    ->default(now()),

                Forms\Components\Select::make('status')
                    ->options([
                        'ACTIVE' => 'Active',
                        'WITHDRAWN' => 'Withdrawn',
                        'GRADUATED' => 'Graduated',
                        'SUSPENDED' => 'Suspended',
                    ])
                    ->default('ACTIVE')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.full_name')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),
                Tables\Columns\TextColumn::make('grade.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('academicYear.year')
                    ->sortable(),
                Tables\Columns\TextColumn::make('enrollment_date')
                    ->date(),
                Tables\Columns\SelectColumn::make('status'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEnrollments::route('/'),
            'create' => Pages\CreateEnrollment::route('/create'),
            'edit' => Pages\EditEnrollment::route('/{record}/edit'),
        ];
    }
}
