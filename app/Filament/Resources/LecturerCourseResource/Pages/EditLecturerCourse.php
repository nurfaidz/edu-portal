<?php

namespace App\Filament\Resources\LecturerCourseResource\Pages;

use App\Filament\Resources\LecturerCourseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLecturerCourse extends EditRecord
{
    protected static string $resource = LecturerCourseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
