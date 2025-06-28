<?php

namespace App\Filament\Resources;

use App\Enums\Region;
use App\Enums\Status;
use App\Filament\Resources\ConferenceResource\Pages;
use App\Filament\Resources\ConferenceResource\RelationManagers;
use App\Models\Conference;
use App\Models\Venue;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ConferenceResource extends Resource
{
    protected static ?string $model = Conference::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(Conference::getForm())
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->description(function (Conference $conf) {
                        return Str::of($conf->description)->limit(40);
                    }),
                // Tables\Columns\TextColumn::make('description')
                //     ->searchable()
                //     ->wrap(),
                Tables\Columns\TextColumn::make('start_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable()
                    ->badge(),
                Tables\Columns\TextColumn::make('region')
                    ->searchable()
                    ->badge(),
                Tables\Columns\TextColumn::make('venue.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('venue.name')
                    ->relationship('venue', 'name', function ($query) {
                        return $query->whereHas('conferences');
                    })
                    ->preload()
                    ->multiple()
            ], layout: FiltersLayout::Modal)
            ->deferFilters()
            ->persistFiltersInSession()
            ->filtersTriggerAction(function (Action $action) {
                return $action->button()->label('Filters');
            })
            // ->deselectAllRecordsWhenFiltered(false)
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('Publish')
                        ->color(Status::Published->getColor())
                        ->icon(Status::Published->getIcon())
                        ->visible(function (Conference $record) {
                            return $record->status !== (Status::Published);
                        })
                        ->action(function (Conference $record) {
                            $record->publish();
                        })
                        ->after(function () {
                            Notification::make()
                                ->duration(2000)
                                ->success()
                                ->title('Published sucessfully')
                                ->body('it\'s published successfully')
                                ->send();
                        }),

                    Tables\Actions\Action::make('Archive')
                        ->color(Status::Archived->getColor())
                        ->icon(Status::Archived->getIcon())
                        ->visible(function (Conference $record) {
                            return $record->status !== Status::Archived;
                        })
                        ->action(function (Conference $record) {
                            $record->archive();
                        })
                        ->requiresConfirmation()
                        ->after(function () {
                            Notification::make()
                                ->duration(2000)
                                ->warning()
                                ->title('Archived sucessfully')
                                ->body('it\'s archived successfully')
                                ->send();
                        }),
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make()
                        ->slideOver(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('Publish Selected')
                        ->color(Status::Published->getColor())
                        ->icon(Status::Published->getIcon())
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each->publish();
                        }),
                    Tables\Actions\BulkAction::make('Archive Selected')
                        ->color(Status::Archived->getColor())
                        ->icon(Status::Archived->getIcon())
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each->archive();
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('Nonesense Action')
                    ->tooltip('This will do nothing')
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
            'index' => Pages\ListConferences::route('/'),
            'create' => Pages\CreateConference::route('/create'),
            'view' => Pages\ViewConference::route('/{record}'),
            // 'edit' => Pages\EditConference::route('/{record}/edit'),
        ];
    }
}
