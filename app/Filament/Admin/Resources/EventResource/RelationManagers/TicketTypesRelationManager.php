<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\EventResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;

final class TicketTypesRelationManager extends RelationManager
{
    protected static string $relationship = 'ticketTypes';
    protected static ?string $recordTitleAttribute = 'name';

    public function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\Textarea::make('description'),
            Forms\Components\TextInput::make('price')->numeric()->required(),
            Forms\Components\TextInput::make('quota')->numeric(),
            Forms\Components\Toggle::make('is_active')->default(true),
        ]);
    }

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Type')->searchable(),
                TextColumn::make('price')->label('Prijs')->money('EUR'),
                TextColumn::make('quota')->label('Quota'),
                TextColumn::make('sold_count')->label('Verkocht'),
                TextColumn::make('is_active')->label('Actief')->boolean(),
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