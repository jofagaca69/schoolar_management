<?php

namespace App\Filament\Resources\TutorsResource\Pages;

use App\Filament\Resources\TutorsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTutors extends ListRecords
{
    protected static string $resource = TutorsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Agregar padre de familia'),
        ];
    }
}
