<?php

declare(strict_types=1);

namespace App\Filament\Organizer\Resources;

use App\Filament\Organizer\Resources\TicketTypeResource\Pages;
use App\Filament\Organizer\Resources\TicketTypeResource\RelationManagers;
use App\Models\TicketType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

final class TicketTypeResource extends Resource
{
    protected static ?string $model = TicketType::class;

    protected static ?string $navigationGroup = 'Event Beheer';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationIcon = 'heroicon-o-tag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Ticket Type Information')
                    ->schema([
                        Forms\Components\Select::make('event_id')
                            ->relationship('event', 'title', function (Builder $query) {
                                $user = auth()->user();
                                $organisationId = $user->organisations()->first()?->id;
                                return $query->where('organisation_id', $organisationId);
                            })
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('€')
                            ->minValue(0),
                        Forms\Components\TextInput::make('quantity')
                            ->required()
                            ->numeric()
                            ->minValue(1),
                        Forms\Components\TextInput::make('sold_quantity')
                            ->numeric()
                            ->minValue(0)
                            ->default(0),
                        Forms\Components\DateTimePicker::make('sale_start_date')
                            ->native(false),
                        Forms\Components\DateTimePicker::make('sale_end_date')
                            ->native(false)
                            ->after('sale_start_date'),
                        Forms\Components\Toggle::make('is_active')
                            ->required()
                            ->default(true),
                        Forms\Components\Textarea::make('benefits')
                            ->label('Benefits/Features')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('event.title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sold_quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('available_quantity')
                    ->numeric()
                    ->getStateUsing(fn ($record) => $record->quantity - $record->sold_quantity)
                    ->sortable(),
                Tables\Columns\TextColumn::make('sale_start_date')
                    ->dateTime('M d, Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sale_end_date')
                    ->dateTime('M d, Y H:i')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('event')
                    ->relationship('event', 'title'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Ticket Types'),
                Tables\Filters\Filter::make('sale_start_date')
                    ->form([
                        Forms\Components\DatePicker::make('sale_from'),
                        Forms\Components\DatePicker::make('sale_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['sale_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('sale_start_date', '>=', $date),
                            )
                            ->when(
                                $data['sale_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('sale_start_date', '<=', $date),
                            );
                    }),
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

    public static function getRelations(): array
    {
        return [
            RelationManagers\TicketsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTicketTypes::route('/'),
            'create' => Pages\CreateTicketType::route('/create'),
            'edit' => Pages\EditTicketType::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        $organisationId = $user->organisations()->first()?->id;

        return parent::getEloquentQuery()
            ->whereHas('event', function (Builder $query) use ($organisationId) {
                $query->where('organisation_id', $organisationId);
            });
    }

    public static function getNavigationBadge(): ?string
    {
        $user = auth()->user();
        $organisationId = $user->organisations()->first()?->id;

        $count = static::getEloquentQuery()
            ->whereHas('event', function (Builder $query) use ($organisationId) {
                $query->where('organisation_id', $organisationId);
            })
            ->count();

        return $count > 0 ? (string) $count : null;
    }
}

