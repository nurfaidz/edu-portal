<?php

namespace App\Filament\Resources;

use App\Enums\Roles\Role;
use App\Filament\Resources\ScheduleResource\Pages;
use App\Filament\Resources\ScheduleResource\RelationManagers;
use App\Models\LecturerCourse;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ScheduleResource extends Resource
{
    protected static ?string $model = LecturerCourse::class;

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
                Tables\Columns\TextColumn::make('lecturer.lecturerProfile.name')
                    ->label('Dosen')
                    ->searchable(),
                Tables\Columns\TextColumn::make('course.name')
                    ->label('Mata Kuliah')
                    ->searchable(),
                Tables\Columns\TextColumn::make('semester')
                    ->label('Semester')
                    ->searchable(),
                Tables\Columns\TextColumn::make('academic_year')
                    ->label('Tahun Akademik')
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('filterSchedule')
                    ->form([
                        Forms\Components\Select::make('lecturer_id')
                            ->label('Dosen')
                            ->searchable()
                            ->options(function () {
                                return \App\Models\Lecturer::pluck('name', 'user_id');
                            }),
                        Forms\Components\Select::make('course_id')
                            ->label('Mata Kuliah')
                            ->searchable()
                            ->options(function () {
                                return \App\Models\Course::pluck('name', 'id');
                            }),
                        Forms\Components\TextInput::make('semester')
                            ->label('Semester')
                            ->minValue(1)
                            ->numeric(),
                        Forms\Components\TextInput::make('academic_year')
                            ->label('Tahun Akademik')
//                            ->default(date('Y'))
                            ->minLength(4)
                            ->maxLength(4)
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(isset($data['lecturer_id']), function ($query) use ($data) {
                                return $query->where('user_id', $data['lecturer_id']);
                            })
                            ->when(isset($data['course_id']), function ($query) use ($data) {
                                return $query->where('course_id', $data['course_id']);
                            })
                            ->when(isset($data['semester']), function ($query) use ($data) {
                                return $query->where('semester', $data['semester']);
                            })
                            ->when(isset($data['academic_year']), function ($query) use ($data) {
                                return $query->where('academic_year', $data['academic_year']);
                            });
                    })
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
            RelationManagers\SchedulesRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSchedules::route('/'),
            'view' => Pages\ViewSchedule::route('/{record}'),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()->role === Role::Superadmin->value;
    }
}
