<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceLecturerResource\Pages;
use App\Filament\Resources\AttendanceLecturerResource\RelationManagers;
use App\Models\Schedule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AttendanceLecturerResource extends Resource
{
    protected static ?string $model = Schedule::class;

    protected static ?string $navigationLabel = 'Daftar Kehadiran';

    protected static ?string $label = 'Kehadiran';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('lecturerCourse.course.name')
                    ->label('Mata Kuliah'),
                Tables\Columns\TextColumn::make('date')
                    ->label('Tanggal')
                    ->formatStateUsing(fn($record) => \Carbon\Carbon::parse($record->date)->locale('id')->isoFormat('dddd, D MMMM Y')),
                Tables\Columns\TextColumn::make('start')
                    ->label('Jam Mulai')
                    ->formatStateUsing(fn($record) => \Carbon\Carbon::parse($record->start)->format('H:i')),
                Tables\Columns\TextColumn::make('end')
                    ->label('Jam Selesai')
                    ->formatStateUsing(fn($record) => \Carbon\Carbon::parse($record->end)->format('H:i')),
                Tables\Columns\TextColumn::make('classroom')
                    ->label('Ruang Kelas'),
            ])
            ->filters([
                Tables\Filters\Filter::make('course_id')
                    ->form([
                        Forms\Components\Select::make('course_id')
                            ->label('Mata Kuliah')
                            ->options(function () {
                                return \App\Models\LecturerCourse::where('user_id', auth()->id())->get()->pluck('course.name', 'course_id');
                            }),
                        Forms\Components\Select::make('classroom')
                            ->label('Ruang Kelas')
                            ->options([
                                \App\Enums\Courses\Classroom::A->value => 'Kelas A',
                                \App\Enums\Courses\Classroom::B->value => 'Kelas B',
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->whereHas('lecturerCourse', function ($query) use ($data) {
                            $query->where('user_id', auth()->id())
                                ->when(isset($data['course_id']), function ($query) use ($data) {
                                    $query->where('course_id', $data['course_id']);
                                });
                        })
                        ->when(isset($data['classroom']), function ($query) use ($data) {
                            $query->where('classroom', $data['classroom']);
                        });
                    })
            ])
            ->deferLoading()
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\AttendanceStudentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendanceLecturers::route('/'),
            'view' => Pages\ViewAttendanceLecturer::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $lastSemester = \App\Models\Schedule::select('semester')
            ->orderBy('semester', 'desc')
            ->first()
            ->semester;

        return parent::getEloquentQuery()
            ->whereHas('lecturerCourse', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->where('semester', $lastSemester)
            ->where('academic_year', now()->year);
    }

    public static function canAccess(): bool
    {
        return auth()->user()->role === \App\Enums\Roles\Role::Lecturer->value;
    }
}
