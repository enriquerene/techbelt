<?php

namespace App\Filament\Resources\GymClassResource\Pages;

use App\Filament\Resources\GymClassResource;
use Filament\Resources\Pages\EditRecord;

class EditGymClass extends EditRecord
{
    protected static string $resource = GymClassResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}