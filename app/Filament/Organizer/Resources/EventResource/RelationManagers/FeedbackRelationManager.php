<?php

declare(strict_types=1);

namespace App\Filament\Organizer\Resources\EventResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

final class FeedbackRelationManager extends RelationManager
{
    protected static string $relationship = 'feedback';

    protected static ?string $recordTitleAttribute = 'attendee_name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('ticket_id')
                    ->relationship('ticket', 'id')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('attendee_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('attendee_email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('overall_rating')
                    ->options([
                        1 => '1 - Poor',
                        2 => '2 - Fair',
                        3 => '3 - Good',
                        4 => '4 - Very Good',
                        5 => '5 - Excellent',
                    ])
                    ->required(),
                Forms\Components\Select::make('organization_rating')
                    ->options([
                        1 => '1 - Poor',
                        2 => '2 - Fair',
                        3 => '3 - Good',
                        4 => '4 - Very Good',
                        5 => '5 - Excellent',
                    ])
                    ->nullable(),
                Forms\Components\Select::make('venue_rating')
                    ->options([
                        1 => '1 - Poor',
                        2 => '2 - Fair',
                        3 => '3 - Good',
                        4 => '4 - Very Good',
                        5 => '5 - Excellent',
                    ])
                    ->nullable(),
                Forms\Components\Select::make('content_rating')
                    ->options([
                        1 => '1 - Poor',
                        2 => '2 - Fair',
                        3 => '3 - Good',
                        4 => '4 - Very Good',
                        5 => '5 - Excellent',
                    ])
                    ->nullable(),
                Forms\Components\Textarea::make('comments')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('would_recommend')
                    ->label('Would recommend to others')
                    ->default(true),
                Forms\Components\Toggle::make('would_attend_again')
                    ->label('Would attend again')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('attendee_name')
            ->columns([
                Tables\Columns\TextColumn::make('attendee_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('attendee_email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('overall_rating')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '1', '2' => 'danger',
                        '3' => 'warning',
                        '4', '5' => 'success',
                    }),
                Tables\Columns\TextColumn::make('organization_rating')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('venue_rating')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('content_rating')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('would_recommend')
                    ->boolean()
                    ->label('Recommend'),
                Tables\Columns\IconColumn::make('would_attend_again')
                    ->boolean()
                    ->label('Attend Again'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('overall_rating')
                    ->options([
                        1 => '1 - Poor',
                        2 => '2 - Fair',
                        3 => '3 - Good',
                        4 => '4 - Very Good',
                        5 => '5 - Excellent',
                    ]),
                Tables\Filters\TernaryFilter::make('would_recommend')
                    ->label('Would Recommend'),
                Tables\Filters\TernaryFilter::make('would_attend_again')
                    ->label('Would Attend Again'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
} 