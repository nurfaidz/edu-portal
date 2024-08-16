<?php

namespace App\Filament\Resources\LecturerCourseResource\Pages;

use App\Filament\Resources\LecturerCourseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLecturerCourses extends ListRecords
{
    protected static string $resource = LecturerCourseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
