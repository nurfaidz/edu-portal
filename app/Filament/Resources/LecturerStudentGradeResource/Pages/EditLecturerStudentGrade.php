<?php

namespace App\Filament\Resources\LecturerStudentGradeResource\Pages;

use App\Filament\Resources\LecturerStudentGradeResource;
use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\View\View;
use Livewire\Features\SupportAttributes\AttributeCollection;
use Milon\Barcode\GS1_128\Section;

class EditLecturerStudentGrade extends EditRecord
{
    protected static string $resource = LecturerStudentGradeResource::class;

    protected static ?string $title = null;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\Section::make('Mata Kuliah')
                    ->columns(2)
                    ->schema([
                        TextInput::make('course.code')
                            ->formatStateUsing(fn($record) => $record->course->code)
                            ->label('Kode Mata Kuliah')
                            ->disabled(),
                        TextInput::make('course.name')
                            ->formatStateUsing(fn($record) => $record->course->name)
                            ->label('Mata Kuliah')
                            ->disabled(),
                        TextInput::make('course.credits')
                            ->formatStateUsing(fn($record) => $record->course->credits)
                            ->label('SKS')
                            ->disabled(),
                        TextInput::make('semester')
                            ->label('Semester')
                            ->disabled(),
                        TextInput::make('academic_year')
                            ->label('Tahun Akademik')
                            ->disabled(),
                    ]),
            ]);
    }

    /**
     * @return string|null
     */
    public function getTitle(): string
    {
        return '';
    }

    public function getBreadcrumb(): string
    {
        return '';
    }

    protected function getFormActions(): array
    {
        return [];
    }
}
