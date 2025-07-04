<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\EventResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;

final class TicketsRelationManager extends RelationManager
{
    protected static string $relationship = 'tickets';
    protected static ?string $recordTitleAttribute = 'uuid';

    public function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('uuid')->disabled(),
            Forms\Components\TextInput::make('status')->required(),
            Forms\Components\TextInput::make('amount_paid')->numeric(),
            Forms\Components\TextInput::make('payment_method'),
            Forms\Components\TextInput::make('payment_id'),
        ]);
    }

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('uuid')->label('UUID')->copyable(),
                TextColumn::make('user.name')->label('Koper')->searchable(),
                TextColumn::make('ticketType.name')->label('Type'),
                TextColumn::make('status')->label('Status')->badge(),
                TextColumn::make('amount_paid')->label('Betaald')->money('EUR'),
                TextColumn::make('paid_at')->label('Betaald op')->dateTime(),
                TextColumn::make('checked_in_at')->label('Check-in')->dateTime(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
} 