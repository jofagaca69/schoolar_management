<?php

namespace App\Filament\Resources\TutorsResource\Pages;

use App\Filament\Resources\TutorsResource;
use App\Models\City;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTutors extends EditRecord
{
    protected static string $resource = TutorsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (isset($data['dni_expedition_city'])) {
            $city_dni_expedition_city = City::with('state.country')->find($data['dni_expedition_city']);

            if ($city_dni_expedition_city) {
                $data['dni_expedition_state_id'] = $city_dni_expedition_city->state->id;
                $data['dni_expedition_country_id'] = $city_dni_expedition_city->state->country_id;
                debugbar()->info($city_dni_expedition_city->state->id);
                debugbar()->info($city_dni_expedition_city->state->country_id);
            }
        }

        if (isset($data['residence_city'])) {
            $city = City::with('state.country')->find($data['residence_city']);

            if ($city) {
                $data['residence_state_id'] = $city->state->id;
                $data['residence_country_id'] = $city->state->country_id;
                debugbar()->info($city->state->id);
                debugbar()->info($city->state->country_id);
            }
        }

        return $data;
    }
}
