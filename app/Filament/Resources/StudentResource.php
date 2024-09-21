<?php

namespace App\Filament\Resources;

use App\Enums\Roles\Role;
use App\Filament\Resources\StudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers;
use App\Models\Student;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms;
use Filament\Notifications\Notification;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class StudentResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationLabel = 'Mahasiswa';

    protected static ?string $label = 'Mahasiswa';

    protected static ?string $navigationGroup = 'Data Master';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Mahasiswa')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Username')
                        ->required(),
                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->required(),
                    Forms\Components\Group::make()
                        ->relationship('studentProfile')
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->label('Nama Mahasiswa')
                                ->required(),
                            Forms\Components\TextInput::make('nim')
                                ->label('Nomor Induk Mahasiswa')
                                ->required(),
                            Forms\Components\Select::make('class')
                                ->label('Ruang Kelas')
                                ->required()
                                ->options([
                                    \App\Enums\Courses\Classroom::A->value => 'A',
                                    \App\Enums\Courses\Classroom::B->value => 'B',
                                ])
                        ]),
                    Forms\Components\TextInput::make('password')
                        ->label('Password')
                        ->password()
                        ->visible(fn($livewire) => $livewire instanceof Pages\CreateStudent)
                        ->required(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('studentProfile.nim')
                    ->label('Nomor Induk Mahasiswa')
                    ->searchable(),
                Tables\Columns\TextColumn::make('studentProfile.name')
                    ->label('Nama Mahasiswa')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\Action::make('importStudent')
                    ->label('Import Mahasiswa')
                    ->icon('heroicon-m-document-arrow-up')
                    ->modalHeading('Import Mahasiswa')
                    ->color('info')
                    ->form([
                        Forms\Components\FileUpload::make('attachment')
                            ->label('File Excel 1')
                            ->rules('mimes:xlsx,xls')
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        try {
                            Excel::import(new \App\Imports\StudentsImport, public_path('storage/' . $data['attachment']));

                            Storage::delete(public_path('storage/' . $data['attachment']));

                            Notification::make()
                                ->title('Berhasil Import Mahasiswa')
                                ->body('Data mahasiswa berhasil diimport')
                                ->success()
                                ->send();

                            return;
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Gagal Import Mahasiswa')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();

                            return;
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([

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
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
            'view' => Pages\ViewStudent::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->student();
    }

    public static function canAccess(): bool
    {
        return auth()->user()->role === Role::Superadmin->value;
    }
}
