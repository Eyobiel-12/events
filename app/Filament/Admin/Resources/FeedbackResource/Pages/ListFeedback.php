<?php

namespace App\Filament\Admin\Resources\FeedbackResource\Pages;

use App\Filament\Admin\Resources\FeedbackResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFeedback extends ListRecords
{
    protected static string $resource = FeedbackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
} 