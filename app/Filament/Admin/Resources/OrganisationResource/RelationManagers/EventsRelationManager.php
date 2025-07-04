<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\OrganisationResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;

final class EventsRelationManager extends RelationManager
{
    protected static string $relationship = 'events';
    protected static ?string $recordTitleAttribute = 'title';

    public function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')->required(),
            Forms\Components\TextInput::make('slug')->required(),
            Forms\Components\Textarea::make('description'),
            Forms\Components\DateTimePicker::make('start_date')->required(),
            Forms\Components\DateTimePicker::make('end_date')->required(),
        ]);
    }

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('Titel')->searchable(),
                TextColumn::make('start_date')->label('Start')->dateTime(),
                TextColumn::make('end_date')->label('Einde')->dateTime(),
                TextColumn::make('status')->label('Status')->badge(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
} 