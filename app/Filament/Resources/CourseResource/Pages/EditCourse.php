<?php

namespace App\Filament\Resources\CourseResource\Pages;

use App\Filament\Resources\CourseResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditCourse extends EditRecord
{
    protected static string $resource = CourseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->action(function() {
                    if ($this->record->lecturerCourse()->exists()) {
                        Notification::make()
                                    ->title('Gagal menghapus')
                                    ->body('Mata kuliah ini masih memiliki data dosen yang terkait.')
                                    ->danger()
                                    ->send();

                        return;
                    }

                    try {
                        $this->record->delete();

                        Notification::make()
                                    ->title('Berhasil menghapus')
                                    ->body('Data mata kuliah berhasil dihapus')
                                    ->success()
                                    ->send();

                        return redirect()->route('filament.admin.resources.courses.index');
                    } catch (\Exception $e) {
                        Notification::make()
                                    ->title('Gagal menghapus')
                                    ->body('Terjadi kesalahan saat menghapus data mata kuliah')
                                    ->danger()
                                    ->send();

                                    return;
                    }
                }),
        ];
    }
}
