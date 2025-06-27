<?php

namespace App\Filament\Resources\ConferenceResource\Pages;

use App\Enums\Status;
use App\Filament\Resources\ConferenceResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListConferences extends ListRecords
{
    protected static string $resource = ConferenceResource::class;

    public function getTabs(): array
    {
        $tabs = ['All' => Tab::make()->icon('heroicon-o-list-bullet')];
        foreach (Status::cases() as $case) {
            $tabs[$case->value] = Tab::make()
                ->query($case->getTab())
                ->icon($case->getIcon());
        }
        return $tabs;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
