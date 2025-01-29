<?php

namespace App\Filament\Resources\LecturerStudentGradeResource\Pages;

use App\Filament\Resources\LecturerStudentGradeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLecturerStudentGrades extends ListRecords
{
    protected static string $resource = LecturerStudentGradeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
