<?php

namespace App\Filament\Resources;

use App\Enums\Roles\Role;
use App\Filament\Resources\LecturerCourseResource\Pages;
use App\Filament\Resources\LecturerCourseResource\RelationManagers;
use App\Models\LecturerCourse;
use App\Models\Schedule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LecturerCourseResource extends Resource
{
    protected static ?string $model = Schedule::class;

    protected static ?string $navigationLabel = 'Jadwal';

    protected static ?string $label = 'Jadwal';

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
                    ->formatStateUsing(fn ($record) => \Carbon\Carbon::parse($record->date)->locale('id')->isoFormat('dddd, D MMMM Y')),
                Tables\Columns\TextColumn::make('start')
                    ->label('Jam Mulai')
                    ->formatStateUsing(fn ($record) => \Carbon\Carbon::parse($record->start)->format('H:i')),
                Tables\Columns\TextColumn::make('end')
                    ->label('Jam Selesai')
                    ->formatStateUsing(fn ($record) => \Carbon\Carbon::parse($record->end)->format('H:i')),
                Tables\Columns\TextColumn::make('classroom')
                    ->label('Ruang Kelas'),
            ])
            ->filters([
                //
            ])
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
            'index' => Pages\ListLecturerCourses::route('/'),
            'view' => Pages\ViewLecturerCourse::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $oddDateStart = date('Y') . '-02-01';
        $oddDateEnd = date('Y') . '-08-31';
        $evenDateStart = date('Y') . '-09-01';
        $evenDateEnd = date('Y') . '-12-31';
        $now = date('Y-m-d');

        $lecturerId = auth()->user()->id;
        return parent::getEloquentQuery()->whereHas('lecturerCourse', function ($query) use ($lecturerId) {
            $oddDateStart = date('Y') . '-02-01';
            $oddDateEnd = date('Y') . '-08-31';
            $evenDateStart = date('Y') . '-09-01';
            $evenDateEnd = date('Y') . '-12-31';
            $now = date('Y-m-d');

            if ($now >= $oddDateStart && $now <= $oddDateEnd) {
                $query->whereRaw('MOD(semester, 2) <> 0')->where('academic_year', now()->year);
            } elseif ($now >= $evenDateStart && $now <= $evenDateEnd) {
                $query->whereRaw('MOD(semester, 2) = 0')->where('academic_year', now()->year);
            }
            $query->where('user_id', $lecturerId);
        })->where('date', now()->format('Y-m-d'));
    }


    public static function canAccess(): bool
    {
        return auth()->user()->role === Role::Lecturer->value;
    }
}
