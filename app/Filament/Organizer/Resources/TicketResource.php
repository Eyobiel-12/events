<?php

declare(strict_types=1);

namespace App\Filament\Organizer\Resources;

use App\Filament\Organizer\Resources\TicketResource\Pages;
use App\Filament\Organizer\Resources\TicketResource\RelationManagers;
use App\Models\Ticket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

final class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $navigationGroup = 'Event Beheer';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Ticket Information')
                    ->schema([
                        Forms\Components\Select::make('ticket_type_id')
                            ->relationship('ticketType', 'name', function (Builder $query) {
                                $user = auth()->user();
                                $organisationId = $user->organisations()->first()?->id;
                                return $query->whereHas('event', function (Builder $q) use ($organisationId) {
                                    $q->where('organisation_id', $organisationId);
                                });
                            })
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
                        Forms\Components\TextInput::make('attendee_phone')
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\Select::make('status')
                            ->options([
                                'available' => 'Available',
                                'reserved' => 'Reserved',
                                'sold' => 'Sold',
                                'used' => 'Used',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required()
                            ->default('available'),
                        Forms\Components\TextInput::make('qr_code')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\Textarea::make('notes')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Payment Information')
                    ->schema([
                        Forms\Components\TextInput::make('amount_paid')
                            ->numeric()
                            ->prefix('€')
                            ->minValue(0),
                        Forms\Components\TextInput::make('payment_method')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('payment_id')
                            ->maxLength(255),
                        Forms\Components\DateTimePicker::make('paid_at')
                            ->native(false),
                        Forms\Components\DateTimePicker::make('checked_in_at')
                            ->native(false),
                        Forms\Components\TextInput::make('checked_in_by')
                            ->maxLength(255),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ticketType.event.title')
                    ->searchable()
                    ->sortable()
                    ->label('Event'),
                Tables\Columns\TextColumn::make('ticketType.name')
                    ->searchable()
                    ->sortable()
                    ->label('Ticket Type'),
                Tables\Columns\TextColumn::make('attendee_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('attendee_email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('attendee_phone')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'gray' => 'available',
                        'warning' => 'reserved',
                        'success' => 'sold',
                        'info' => 'used',
                        'danger' => 'cancelled',
                    ]),
                Tables\Columns\TextColumn::make('amount_paid')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('qr_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('paid_at')
                    ->dateTime('M d, Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('checked_in_at')
                    ->dateTime('M d, Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('ticket_type')
                    ->relationship('ticketType', 'name'),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'available' => 'Available',
                        'reserved' => 'Reserved',
                        'sold' => 'Sold',
                        'used' => 'Used',
                        'cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\TernaryFilter::make('paid_at')
                    ->label('Paid Tickets'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('check_in')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (Ticket $record) => $record->update([
                        'checked_in_at' => now(),
                        'checked_in_by' => auth()->user()->name,
                    ]))
                    ->visible(fn (Ticket $record) => $record->status === 'sold' && !$record->checked_in_at),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        $organisationId = $user->organisations()->first()?->id;

        return parent::getEloquentQuery()
            ->whereHas('ticketType.event', function (Builder $query) use ($organisationId) {
                $query->where('organisation_id', $organisationId);
            });
    }

    public static function getNavigationBadge(): ?string
    {
        $user = auth()->user();
        $organisationId = $user->organisations()->first()?->id;

        $count = static::getEloquentQuery()
            ->whereHas('ticketType.event', function (Builder $query) use ($organisationId) {
                $query->where('organisation_id', $organisationId);
            })
            ->count();

        return $count > 0 ? (string) $count : null;
    }
}
