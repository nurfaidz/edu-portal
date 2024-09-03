<?php

namespace App\Filament\Resources;

use App\Enums\Roles\Role;
use App\Filament\Resources\LecturerRescheduleResource\Pages;
use App\Filament\Resources\LecturerRescheduleResource\RelationManagers;
use App\Models\Schedule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LecturerRescheduleResource extends Resource
{
    protected static ?string $model = Schedule::class;

    protected static ?string $navigationLabel = 'Reschedule';

    protected static ?string $label = 'Reschedule';

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
                        if (isset($data['course_id'])) {
                            return $query->whereHas('lecturerCourse', function ($query) use ($data) {
                                $query->where('user_id', auth()->id())->where('course_id', $data['course_id']);
                            })
                                ->where('classroom', $data['classroom']);
                        }
                        return $query;
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLecturerReschedules::route('/'),
            'view' => Pages\ViewLecturerReschedule::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->reschedule()->whereHas('lecturerCourse', function ($query) {
            $query->where('user_id', auth()->id());
        });
    }


    public static function canAccess(): bool
    {
        return auth()->user()->role === Role::Lecturer->value;
    }
}
