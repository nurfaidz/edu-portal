<?php

namespace App\Filament\Resources\ScheduleResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SchedulesRelationManager extends RelationManager
{
    protected static string $relationship = 'schedules';

    protected static ?string $title = 'Jadwal';

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
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('Tanggal')
                    ->formatStateUsing(fn($record) => \Carbon\Carbon::parse($record->date)->locale('id')->isoFormat('dddd, D MMMM Y')),
                Tables\Columns\TextColumn::make('start')
                    ->label('Jam Mulai'),
                Tables\Columns\TextColumn::make('end')
                    ->label('Jam Selesai'),
                Tables\Columns\TextColumn::make('classroom')
                    ->label('Ruang Kelas'),
            ])
            ->filters([
                Tables\Filters\Filter::make('filterSchedule')
                    ->form([
                        Forms\Components\Select::make('classroom')
                            ->label('Ruang Kelas')
                            ->searchable()
                            ->options([
                                \App\Enums\Courses\Classroom::A->value => 'Kelas A',
                                \App\Enums\Courses\Classroom::B->value => 'Kelas B',
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['classroom'], fn($query, $classroom) => $query->where('classroom', $classroom));
                    }),
            ])
            ->headerActions([
                //
            ])
            ->actions([
               //
            ])
            ->bulkActions([
                //
            ]);
    }
}
