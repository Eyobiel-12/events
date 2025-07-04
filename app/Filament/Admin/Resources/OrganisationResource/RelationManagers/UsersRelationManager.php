<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\OrganisationResource\RelationManagers;

use App\Models\User;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;

final class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';
    protected static ?string $recordTitleAttribute = 'name';

    public function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\TextInput::make('email')->email()->required(),
        ]);
    }

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Naam')->searchable(),
                TextColumn::make('email')->label('E-mail')->searchable(),
                TextColumn::make('pivot.role')->label('Rol')->badge(),
            ])
            ->headerActions([
                AttachAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DetachAction::make(),
            ]);
    }
} 