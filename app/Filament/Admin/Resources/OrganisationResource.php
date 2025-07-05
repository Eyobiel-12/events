<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\OrganisationResource\Pages;
use App\Models\Organisation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

final class OrganisationResource extends Resource
{
    protected static ?string $model = Organisation::class;

    protected static ?string $navigationGroup = 'Platformbeheer';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Organisatie Informatie')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Naam')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Forms\Components\Textarea::make('description')
                            ->label('Beschrijving')
                            ->maxLength(1000)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('phone')
                            ->label('Telefoon')
                            ->tel()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('website')
                            ->label('Website')
                            ->url()
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Adres Informatie')
                    ->schema([
                        Forms\Components\TextInput::make('address')
                            ->label('Adres')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('city')
                            ->label('Stad')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('postal_code')
                            ->label('Postcode')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('country')
                            ->label('Land')
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Actief')
                            ->default(true),

                        Forms\Components\DatePicker::make('created_at')
                            ->label('Aangemaakt op')
                            ->disabled(),

                        Forms\Components\DatePicker::make('updated_at')
                            ->label('Laatst bijgewerkt')
                            ->disabled(),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Naam')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('city')
                    ->label('Stad')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('events_count')
                    ->label('Events')
                    ->counts('events')
                    ->sortable(),

                Tables\Columns\TextColumn::make('users_count')
                    ->label('Gebruikers')
                    ->counts('users')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Aangemaakt')
                    ->dateTime('d-m-Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('Alle organisaties')
                    ->trueLabel('Alleen actieve')
                    ->falseLabel('Alleen inactieve'),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Vanaf'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Tot'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('users')
                    ->label('Gebruikers')
                    ->icon('heroicon-m-users')
                    ->url(fn (Organisation $record): string => route('filament.admin.resources.organisations.users', $record)),
                Tables\Actions\Action::make('events')
                    ->label('Events')
                    ->icon('heroicon-m-calendar')
                    ->url(fn (Organisation $record): string => route('filament.admin.resources.organisations.events', $record)),
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
            'index' => Pages\ListOrganisations::route('/'),
            'create' => Pages\CreateOrganisation::route('/create'),
            'edit' => Pages\EditOrganisation::route('/{record}/edit'),
        ];
    }
}
