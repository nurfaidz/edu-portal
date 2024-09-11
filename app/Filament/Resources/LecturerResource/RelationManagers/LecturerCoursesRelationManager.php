<?php

namespace App\Filament\Resources\LecturerResource\RelationManagers;

use App\Enums\Courses\Type;
use App\Models\LecturerCourse;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LecturerCoursesRelationManager extends RelationManager
{
    protected static string $relationship = 'lecturerCourses';

    protected static ?string $title = 'Mata Kuliah Dosen';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('course_id')
                            ->label('Mata Kuliah')
                            ->options(
                                \App\Models\Course::query()
                                    ->pluck('name', 'id')
                                    ->toArray()
                            )
                            ->searchable()
                            ->required(),
                        Forms\Components\TextInput::make('academic_year')
                            ->label('Tahun Akademik')
                            ->default(date('Y'))
                            ->minLength(4)
                            ->maxLength(4)
                            ->numeric()
                            ->required(),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('course.name')
                    ->label('Mata Kuliah'),
                Tables\Columns\TextColumn::make('course.code')
                    ->label('Kode')
                    ->searchable(),
                Tables\Columns\TextColumn::make('semester')
                    ->label('Semester'),
                Tables\Columns\TextColumn::make('academic_year')
                    ->label('Tahun Akademik'),
//                Tables\Columns\TextColumn::make('course.credits')
//                    ->label('SKS'),
//                Tables\Columns\TextColumn::make('course.semester')
//                    ->label('Semester'),
//                Tables\Columns\TextColumn::make('course.type')
//                    ->label('Tipe')
//                    ->formatStateUsing(function ($record) {
//                        return match ($record->course->type) {
//                            Type::Mandatory->value => 'Wajib',
//                            Type::Elective->value => 'Pilihan',
//                        };
//                    }),
            ])
            ->filters([
                Tables\Filters\Filter::make('filterStudent')
                    ->form([
                        Forms\Components\TextInput::make('semester')
                            ->label('Semester')
                            ->minValue(1)
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('academic_year')
                            ->label('Tahun Akademik')
                            ->default(date('Y'))
                            ->minLength(4)
                            ->maxLength(4)
                            ->numeric()
                            ->required(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['semester'], function ($query, $semester) {
                                return $query->where('semester', $semester);
                            })
                            ->when($data['academic_year'], function ($query, $academicYear) {
                                return $query->where('academic_year', $academicYear);
                            });
                    }),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Mata Kuliah')
                    ->modalHeading('Tambah Mata Kuliah Dosen')
                    ->action(function (array $data) {
                        try {
                            // cek apakah mata kuliah sudah ada
                            if (LecturerCourse::where('course_id', $data['course_id'])
                                ->where('user_id', $this->ownerRecord->id)
                                ->where('academic_year', $data['academic_year'])
                                ->exists()) {
                                Notification::make()
                                    ->title('Gagal menambahkan')
                                    ->body('Mata kuliah dosen pada tahun akademik ini sudah ada')
                                    ->danger()
                                    ->send();

                                return;
                            }

                            $course = \App\Models\Course::find($data['course_id']);
                            LecturerCourse::create([
                                'user_id' => $this->ownerRecord->id,
                                'course_id' => $course->id,
                                'semester' => $course->semester,
                                'academic_year' => $data['academic_year'],
                            ]);

                            Notification::make()
                                ->title('Berhasil menambahkan')
                                ->body('Mata kuliah dosen berhasil ditambahkan')
                                ->success()
                                ->send();

                            return;

                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Gagal menambahkan')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();

                            return;
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->modalHeading('Menghapus Mata Kuliah Dosen akan otomatis menghapus semua jadwal yang terkait')
                    ->action(function ($record) {
                        try {
                            $schedules = \App\Models\Schedule::where('lecturer_course_id', $record->id)
                                ->where('semester', $record->semester)
                                ->where('academic_year', $record->academic_year)
                                ->get();

                            if ($schedules->count() > 0) {
                                $schedules->each(function ($schedule) {
                                    $schedule->delete();
                                });
                            }

                            $record->delete();

                            Notification::make()
                                ->title('Berhasil menghapus')
                                ->body('Mata kuliah dosen dan semua jadwal yang terkait berhasil dihapus')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Gagal menghapus')
                                ->body('Terjadi kesalahan saat menghapus mata kuliah dosen')
                                ->danger()
                                ->send();
                        }
                    })
            ])
            ->deferLoading()
            ->bulkActions([
                //
            ]);
    }
}
