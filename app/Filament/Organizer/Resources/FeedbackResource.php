<?php

declare(strict_types=1);

namespace App\Filament\Organizer\Resources;

use App\Filament\Organizer\Resources\FeedbackResource\Pages;
use App\Models\Feedback;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

final class FeedbackResource extends Resource
{
    protected static ?string $model = Feedback::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Analytics';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Feedback Information')
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
                Tables\Filters\SelectFilter::make('event')
                    ->relationship('event', 'title'),
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
            'index' => Pages\ListFeedback::route('/'),
            'create' => Pages\CreateFeedback::route('/create'),
            'edit' => Pages\EditFeedback::route('/{record}/edit'),
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
