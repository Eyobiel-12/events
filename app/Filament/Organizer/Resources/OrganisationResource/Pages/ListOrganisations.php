<?php

namespace App\Filament\Organizer\Resources\OrganisationResource\Pages;

use App\Filament\Organizer\Resources\OrganisationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOrganisations extends ListRecords
{
    protected static string $resource = OrganisationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
