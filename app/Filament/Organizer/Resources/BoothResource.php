<?php

declare(strict_types=1);

namespace App\Filament\Organizer\Resources;

use App\Filament\Organizer\Resources\BoothResource\Pages;
use App\Models\Booth;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

final class BoothResource extends Resource
{
    protected static ?string $model = Booth::class;

    protected static ?string $navigationGroup = 'Event Details';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Booth Information')
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
                        Forms\Components\Select::make('vendor_id')
                            ->relationship('vendor', 'name', function (Builder $query) {
                                $user = auth()->user();
                                $organisationId = $user->organisations()->first()?->id;
                                return $query->where('organisation_id', $organisationId);
                            })
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('number')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\Textarea::make('description')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\Select::make('size')
                            ->options([
                                'small' => 'Small',
                                'medium' => 'Medium',
                                'large' => 'Large',
                            ])
                            ->required()
                            ->default('medium'),
                        Forms\Components\TextInput::make('price')
                            ->numeric()
                            ->prefix('€')
                            ->maxValue(4294967295)
                            ->default(0),
                        Forms\Components\Select::make('status')
                            ->options([
                                'available' => 'Available',
                                'reserved' => 'Reserved',
                                'occupied' => 'Occupied',
                            ])
                            ->required()
                            ->default('available'),
                        Forms\Components\TagsInput::make('amenities')
                            ->placeholder('Add amenities...'),
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
                Tables\Columns\TextColumn::make('number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('vendor.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('size')
                    ->colors([
                        'gray' => 'small',
                        'blue' => 'medium',
                        'green' => 'large',
                    ]),
                Tables\Columns\TextColumn::make('price')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'available',
                        'warning' => 'reserved',
                        'danger' => 'occupied',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('event')
                    ->relationship('event', 'title'),
                Tables\Filters\SelectFilter::make('size')
                    ->options([
                        'small' => 'Small',
                        'medium' => 'Medium',
                        'large' => 'Large',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'available' => 'Available',
                        'reserved' => 'Reserved',
                        'occupied' => 'Occupied',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListBooths::route('/'),
            'create' => Pages\CreateBooth::route('/create'),
            'edit' => Pages\EditBooth::route('/{record}/edit'),
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
