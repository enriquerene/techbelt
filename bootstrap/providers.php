<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\Filament\AdminPanelProvider::class,
    App\Providers\Filament\StaffPanelProvider::class,
    App\Providers\FortifyServiceProvider::class,
    Filament\Actions\ActionsServiceProvider::class,
    Filament\FilamentServiceProvider::class,
    Filament\Forms\FormsServiceProvider::class,
    Filament\Infolists\InfolistsServiceProvider::class,
    Filament\Notifications\NotificationsServiceProvider::class,
    Filament\Tables\TablesServiceProvider::class,
    Filament\Widgets\WidgetsServiceProvider::class,
    Livewire\LivewireServiceProvider::class,
];
