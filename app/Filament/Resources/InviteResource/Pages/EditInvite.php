<?php

namespace App\Filament\Resources\InviteResource\Pages;

use App\Filament\Resources\InviteResource;
use Filament\Resources\Pages\EditRecord;

class EditInvite extends EditRecord
{
    protected static string $resource = InviteResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}