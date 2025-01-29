<?php

namespace App\Filament\Resources\LecturerStudentGradeResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class StudentCoursesRelationManager extends RelationManager
{
    protected static string $relationship = 'studentCourses';

    protected static ?string $title = 'Mahasiswa';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {

                $monthDay = date('m-d');

                return $query->where(function ($query) use ($monthDay) {
                    if ($monthDay >= '02-01' && $monthDay <= '08-31') {
                        $query->whereRaw('MOD(semester, 2) = 0')
                            ->where('academic_year', now()->year);
                    } else {
                        $query->whereRaw('MOD(semester, 2) <> 0')
                            ->where('academic_year', now()->year);
                    }
                });
            })
            ->columns([
                Tables\Columns\TextColumn::make('student.name')
                    ->label('NIM')
                    ->searchable(),
                Tables\Columns\TextColumn::make('student.studentProfile.name')
                    ->label('Nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('numeric_grade')
                    ->label('Nilai'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('edit')
                    ->label('Ubah')
                    ->modalHeading('Input Nilai')
                    ->modalWidth('2xl')
                    ->form([
                        Forms\Components\TextInput::make('numeric_grade')
                            ->label('Nilai')
                            ->numeric()
                            ->rules([
                                function (callable $get) {
                                    return function (string $attribute, $value, callable $fail) use ($get) {
                                        // if quantity any '-' example -1, then fail
                                        if ($value < 1) {
                                            $fail('Jumlah tidak boleh kurang dari 1.');
                                        }
                                    };
                                }
                            ])
                            ->required(),
                    ])
                    ->action(function ($record, $data) {
                        try {
                            DB::transaction(function () use ($record, $data) {
                                $record->update($data);
                            });

                            DB::commit();

                            Notification::make()
                                ->title('Berhasil')
                                ->body('Input nilai berhasil disimpan!')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            DB::rollBack();

                            Notification::make()
                                ->title('Gagal')
                                ->body('Input nilai gagal disimpan!')
                                ->error($e->getMessage())
                                ->send();
                        }
                    })
                    ->requiresConfirmation()
                    ->modalDescription('Pastikan nilai yang dimasukkan benar!')
                    ->modalSubmitAction(),
            ])
            ->bulkActions([
                //
            ]);
    }
}
