<?php

namespace App\Filament\Resources\LecturerResource\Pages;

use App\Filament\Resources\LecturerResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditLecturer extends EditRecord
{
    protected static string $resource = LecturerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->action(function() {
                    if ($this->record->lecturerCourses()->exists()) {
                        Notification::make()
                                    ->title('Gagal menghapus')
                                    ->body('Dosen ini masih terdaftar di mata kuliah.')
                                    ->danger()
                                    ->send();

                        return;
                    }

                    try {
                        $this->record->delete();

                        Notification::make()
                                    ->title('Berhasil menghapus')
                                    ->body('Data dosen berhasil dihapus')
                                    ->success()
                                    ->send();

                        return redirect()->route('filament.admin.resources.lecturers.index');
                    } catch (\Exception $e) {
                        Notification::make()
                                    ->title('Gagal menghapus')
                                    ->body('Terjadi kesalahan saat menghapus data dosen')
                                    ->danger()
                                    ->send();

                                    return;
                    }
                }),
        ];
    }
}
