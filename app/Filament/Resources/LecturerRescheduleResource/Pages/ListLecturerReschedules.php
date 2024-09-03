<?php

namespace App\Filament\Resources\LecturerRescheduleResource\Pages;

use App\Filament\Resources\LecturerRescheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLecturerReschedules extends ListRecords
{
    protected static string $resource = LecturerRescheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
