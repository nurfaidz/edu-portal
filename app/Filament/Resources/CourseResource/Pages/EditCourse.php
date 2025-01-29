<?php

namespace App\Filament\Resources\CourseResource\Pages;

use App\Filament\Resources\CourseResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Livewire\Form;
use function Laravel\Prompts\clear;

class EditCourse extends EditRecord
{
    protected static string $resource = CourseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->modalHeading('Hapus Mata Kuliah')
                ->modalDescription('Dengan menghapus mata kuliah ini, maka data yang terkait dengan mata kuliah ini akan ikut terhapus, seperti jadwal, dosen, dan mahasiswa yang terdaftar di mata kuliah ini.')
                ->action(function() {
                    try {
                        if ($this->record->schedules()->exists()) {
                            $this->record->schedules()->delete();
                        }

                        if ($this->record->lecturerCourse()->exists()) {
                            $this->record->lecturerCourse()->delete();
                        }

                        if ($this->record->studentCourses()->exists()) {
                            $this->record->studentCourses()->delete();
                        }

                        $this->record->delete();

                        Notification::make()
                            ->title('Berhasil menghapus')
                            ->body('Mata kuliah dan semua data terkait berhasil dihapus')
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
            Actions\Action::make('studentCourses')
                    ->label('Tambahkan Mahasiswa')
                    ->modalHeading('Tambahkan Mahasiswa di Mata Kuliah ini')
                    ->form([
                        Forms\Components\Select::make('student_id')
                            ->label('Mahasiswa')
                            ->options(function () {
                                return \App\Models\Student::all()->pluck('nim_name', 'user_id');
                            })
                            ->searchable()
                            ->getSearchResultsUsing(function (string $search) {
                                return \App\Models\Student::query()
                                    ->where('nim', 'like', "%{$search}%")
                                    ->orWhere('name', 'like', "%{$search}%")
                                    ->get()
                                    ->pluck('nim_name', 'user_id');
                            })
                            ->multiple()
                            ->required(),
                        Forms\Components\TextInput::make('academic_year')
                            ->label('Tahun Akademik')
                            ->default(date('Y'))
                            ->minLength(4)
                            ->maxLength(4)
                            ->numeric()
                            ->required(),
                    ])
                    ->modalSubmitActionLabel('Tambahkan')
                    ->action(function (array $data) {
                        try {
                            foreach ($data['student_id'] as $studentId) {
                                $userId = \App\Models\User::where('id', $studentId)->first()->id;

                                $studentCourse = \App\Models\StudentCourse::where('user_id', $userId)
                                    ->where('course_id', $this->record->id)
                                    ->where('semester', $this->record->semester)
                                    ->where('academic_year', $data['academic_year'])
                                    ->first();

                                if ($studentCourse) {
                                    continue;
                                }

                                \App\Models\StudentCourse::create([
                                    'user_id' => $userId,
                                    'course_id' => $this->record->id,
                                    'semester' => $this->record->semester,
                                    'academic_year' => $data['academic_year'],
                                ]);
                            }

                            Notification::make()
                                ->title('Berhasil menambahkan')
                                ->body('Mahasiswa berhasil ditambahkan ke mata kuliah ini')
                                ->success()
                                ->send();

                            return redirect()->route('filament.admin.resources.courses.edit', $this->record);

                        } catch (\Exception $e) {
                            Notification::make()
                            ->title('Gagal menambahkan')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();

                            return;
                        }
                    })
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if ($record->lecturerCourse()->where('academic_year', now()->year)->exists()) {
            session()->flash('error', 'update_failed');

            return $record;
        } elseif ($record->studentCourses()->where('academic_year', now()->year)->exists()) {
            session()->flash('error', 'update_failed');

            return $record;
        } else {
            $record->update($data);

            return $record;
        }
    }

    protected function getSavedNotification(): ?Notification
    {
        if (session()->get('error') === 'update_failed') {
            session()->forget('error');

            return Notification::make()
                ->danger()
                ->title('Gagal')
                ->body('Tidak dapat mengubah mata kuliah yang sudah memiliki dosen atau mahasiswa terdaftar');
        }

        return Notification::make()
            ->success()
            ->title('Berhasil')
            ->body('Data mata kuliah berhasil disimpan');
    }

    protected function getRedirectUrl(): ?string
    {
        return route('filament.admin.resources.courses.edit', $this->record);
    }


}
