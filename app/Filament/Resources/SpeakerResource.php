<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SpeakerResource\Pages;
use App\Filament\Resources\SpeakerResource\RelationManagers;
use App\Models\Speaker;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists;


class SpeakerResource extends Resource
{
    protected static ?string $model = Speaker::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('avatar')
                    ->avatar()
                    ->imageEditor()
                    ->maxSize(2 * 1024 * 1024)
                    ->directory('avatars'),
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required(),
                Forms\Components\MarkdownEditor::make('bio')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('twitter_handle')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('twitter_handle')
                    ->searchable(),
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
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->slideOver(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            // Section 1: Main Profile Information
            // This section uses a grid to place the avatar and the main details side-by-side.
            Infolists\Components\Section::make('Profile Information')
                ->columns(3)
                ->schema([
                    // Column 1: Avatar
                    Infolists\Components\ImageEntry::make('avatar')
                        ->label('Avatar')
                        ->circular() // Makes the avatar image round for a nicer look
                        ->columnSpan(1),

                    // Column 2: User Details
                    // A nested grid to stack the name, email, and Twitter handle neatly.
                    Infolists\Components\Grid::make(1)
                        ->columnSpan(2)
                        ->columns(2)
                        ->schema([
                            Infolists\Components\TextEntry::make('name')
                                ->label('Full Name')
                                ->icon('heroicon-o-user-circle'),

                            Infolists\Components\TextEntry::make('email')
                                ->label('Email Address')
                                ->icon('heroicon-o-envelope')
                                // Create a clickable mailto: link
                                ->url(fn(string $state): string => "mailto:{$state}"),

                            Infolists\Components\TextEntry::make('twitter_handle')
                                ->label('Twitter')
                                ->icon('heroicon-o-at-symbol')
                                // Create a clickable link to the user's Twitter profile
                                ->url(fn(string $state): string => "https://twitter.com/{$state}", true),
                            Infolists\Components\TextEntry::make('has_spoken')
                                ->getStateUsing(function (Speaker $record) {
                                    return $record->talks()->count() > 0 ? 'Speaker' : 'Has Not Spoken';
                                })
                                ->badge()
                                ->color(function ($state) {
                                    return $state === 'Speaker' ? 'success' : 'primary';
                                })
                        ]),
                ]),

            // Section 2: Biography
            // This section is dedicated to the user's bio, spanning the full width.
            Infolists\Components\Section::make('Biography')
                ->schema([
                    Infolists\Components\TextEntry::make('bio')
                        ->label('User Bio')
                        ->prose() // Improves readability for long text
                        ->columnSpanFull()
                        ->markdown(), // Ensures the bio takes the full width
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
            'index' => Pages\ListSpeakers::route('/'),
            'create' => Pages\CreateSpeaker::route('/create'),
            'view' => Pages\ViewSpeaker::route('/{record}'),
            // 'edit' => Pages\EditSpeaker::route('/{record}/edit'),
        ];
    }
}
