<?php

namespace App\Filament\Organizer\Resources\BoothResource\Pages;

use App\Filament\Organizer\Resources\BoothResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBooth extends EditRecord
{
    protected static string $resource = BoothResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
