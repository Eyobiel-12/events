<?php

declare(strict_types=1);

namespace App\Filament\Organizer\Resources;

use App\Filament\Organizer\Resources\TicketScanResource\Pages;
use App\Models\TicketScan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

final class TicketScanResource extends Resource
{
    protected static ?string $model = TicketScan::class;

    protected static ?string $navigationIcon = 'heroicon-o-qr-code';

    protected static ?string $navigationGroup = 'Ticketing';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Ticket Scan Information')
                    ->schema([
                        Forms\Components\Select::make('ticket_id')
                            ->relationship('ticket', 'id', function (Builder $query) {
                                $user = auth()->user();
                                $organisationId = $user->organisations()->first()?->id;
                                return $query->whereHas('ticketType.event', function (Builder $q) use ($organisationId) {
                                    $q->where('organisation_id', $organisationId);
                                });
                            })
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('event_id')
                            ->relationship('event', 'title', function (Builder $query) {
                                $user = auth()->user();
                                $organisationId = $user->organisations()->first()?->id;
                                return $query->where('organisation_id', $organisationId);
                            })
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('scanned_by')
                            ->relationship('scannedBy', 'name')
                            ->required()
                            ->default(auth()->id())
                            ->searchable()
                            ->preload(),
                        Forms\Components\DateTimePicker::make('scanned_at')
                            ->required()
                            ->default(now()),
                        Forms\Components\TextInput::make('location')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('notes')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'valid' => 'Valid',
                                'invalid' => 'Invalid',
                                'already_used' => 'Already Used',
                            ])
                            ->required()
                            ->default('valid'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ticket.id')
                    ->label('Ticket ID')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('event.title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('scannedBy.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('scanned_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('location')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'valid',
                        'danger' => 'invalid',
                        'warning' => 'already_used',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('event')
                    ->relationship('event', 'title'),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'valid' => 'Valid',
                        'invalid' => 'Invalid',
                        'already_used' => 'Already Used',
                    ]),
                Tables\Filters\Filter::make('scanned_at')
                    ->form([
                        Forms\Components\DatePicker::make('scanned_from'),
                        Forms\Components\DatePicker::make('scanned_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['scanned_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('scanned_at', '>=', $date),
                            )
                            ->when(
                                $data['scanned_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('scanned_at', '<=', $date),
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
            ->defaultSort('scanned_at', 'desc');
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
            'index' => Pages\ListTicketScans::route('/'),
            'create' => Pages\CreateTicketScan::route('/create'),
            'edit' => Pages\EditTicketScan::route('/{record}/edit'),
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

        return static::getEloquentQuery()
            ->whereHas('event', function (Builder $query) use ($organisationId) {
                $query->where('organisation_id', $organisationId);
            })
            ->count();
    }
}
