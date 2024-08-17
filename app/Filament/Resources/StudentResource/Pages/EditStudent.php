<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditStudent extends EditRecord
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->action(function() {
                    if ($this->record->studentCourses()->exists()) {
                        Notification::make()
                                    ->title('Gagal menghapus')
                                    ->body('Mahasiswa ini masih terdaftar di mata kuliah.')
                                    ->danger()
                                    ->send();

                        return;
                    }

                    try {
                        $this->record->delete();

                        Notification::make()
                                    ->title('Berhasil menghapus')
                                    ->body('Data mahasiswa berhasil dihapus')
                                    ->success()
                                    ->send();

                        return redirect()->route('filament.admin.resources.students.index');
                    } catch (\Exception $e) {
                        Notification::make()
                                    ->title('Gagal menghapus')
                                    ->body('Terjadi kesalahan saat menghapus data mahasiswa')
                                    ->danger()
                                    ->send();

                                    return;
                    }
                }),
        ];
    }
}
