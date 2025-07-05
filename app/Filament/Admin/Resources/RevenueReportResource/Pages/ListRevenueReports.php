<?php

namespace App\Filament\Admin\Resources\RevenueReportResource\Pages;

use App\Filament\Admin\Resources\RevenueReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRevenueReports extends ListRecords
{
    protected static string $resource = RevenueReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
