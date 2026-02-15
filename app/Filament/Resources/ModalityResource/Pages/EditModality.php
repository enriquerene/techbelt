<?php

namespace App\Filament\Resources\ModalityResource\Pages;

use App\Filament\Resources\ModalityResource;
use Filament\Resources\Pages\EditRecord;

class EditModality extends EditRecord
{
    protected static string $resource = ModalityResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}