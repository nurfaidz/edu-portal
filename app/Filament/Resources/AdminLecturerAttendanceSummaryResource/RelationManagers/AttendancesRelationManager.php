<?php

namespace App\Filament\Resources\AdminLecturerAttendanceSummaryResource\RelationManagers;

use App\States\AttendanceStatus\Absent;
use App\States\AttendanceStatus\Excused;
use App\States\AttendanceStatus\Pending;
use App\States\AttendanceStatus\Present;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AttendancesRelationManager extends RelationManager
{
    protected static string $relationship = 'attendances';

    protected static ?string $title = 'Absensi';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->modifyQueryUsing(function (Builder $query) {
                $oddDateStart = date('Y') . '-02-01';
                $oddDateEnd = date('Y') . '-08-31';
                $evenDateStart = date('Y') . '-09-01';
                $evenDateEnd = date('Y') . '-01-31';
                $now = date('Y-m-d');

                return $query->whereHas('schedule', function ($query) use ($oddDateStart, $oddDateEnd, $evenDateStart, $evenDateEnd, $now) {
                    if ($now >= $oddDateStart && $now <= $oddDateEnd) {
                        $query->whereRaw('MOD(semester, 2) <> 0')->where('academic_year', now()->year);
                    } elseif ($now >= $evenDateStart && $now <= $evenDateEnd) {
                        $query->whereRaw('MOD(semester, 2) = 0')->where('academic_year', now()->year);
                    }
                });
            })
            ->columns([
                Tables\Columns\TextColumn::make('schedule.lecturerCourse.course.name')
                    ->label('Mata Kuliah'),
                Tables\Columns\TextColumn::make('schedule.class')
                    ->label('Kelas'),
                Tables\Columns\TextColumn::make('schedule.semester')
                    ->label('Semester'),
                Tables\Columns\TextColumn::make('schedule.academic_year')
                    ->label('Tahun Akademik'),
                Tables\Columns\TextColumn::make('expired_at')
                    ->label('Batas Waktu Absen'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status Kehadiran')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Present::$name => 'success',
                        Absent::$name => 'danger',
                        Excused::$name => 'warning',
                        Pending::$name => 'gray'
                    }),
            ])
            ->filters([
                Tables\Filters\Filter::make('filterAttendance')
                    ->form([
                        Forms\Components\Select::make('course_id')
                            ->label('Mata Kuliah')
                            ->options(function () {
                                $lecturerId = $this->ownerRecord->user_id;
                                return \App\Models\LecturerCourse::where('user_id', $lecturerId)->get()->pluck('course.name', 'course_id');
                            }),
                        Forms\Components\Select::make('classroom')
                            ->label('Ruang Kelas')
                            ->options([
                                \App\Enums\Courses\Classroom::A->value => 'Kelas A',
                                \App\Enums\Courses\Classroom::B->value => 'Kelas B',
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->whereHas('schedule', function ($query) use ($data) {
                            $query->whereHas('lecturerCourse', function ($query) use ($data) {
                                $query->when(isset($data['course_id']), function ($query) use ($data) {
                                    $query->where('course_id', $data['course_id']);
                                });
                            })
                                ->when(isset($data['classroom']), function ($query) use ($data) {
                                    $query->where('classroom', $data['classroom']);
                                });
                        });
                    })
            ])
            ->deferLoading()
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
