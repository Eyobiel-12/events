<?php

namespace App\Filament\Organizer\Resources\TicketScanResource\Pages;

use App\Filament\Organizer\Resources\TicketScanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTicketScan extends EditRecord
{
    protected static string $resource = TicketScanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
