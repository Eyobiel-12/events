<?php

namespace App\Filament\Organizer\Resources\OrganisationResource\Pages;

use App\Filament\Organizer\Resources\OrganisationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrganisation extends EditRecord
{
    protected static string $resource = OrganisationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
