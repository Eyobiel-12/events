<?php

namespace App\Filament\Organizer\Resources\BoothResource\Pages;

use App\Filament\Organizer\Resources\BoothResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBooths extends ListRecords
{
    protected static string $resource = BoothResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
