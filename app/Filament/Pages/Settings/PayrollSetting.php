<?php

namespace App\Filament\Pages\Settings;

use App\Enums\Roles\Role;
use App\Settings\PayrollLecturer;
use Filament\Forms\Form;
use Filament\Forms;
use Filament\Pages\Page;
use Filament\Pages\SettingsPage;

class PayrollSetting extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = PayrollLecturer::class;

    protected static ?string $title = 'Setting Upah';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Setting Upah')
                    ->schema([
                    Forms\Components\TextInput::make('amount')
                        ->label('Upah Dosen')
                        ->helperText('Upah dosen per pertemuan')
                        ->minValue(0)
                        ->numeric()
                        ->required(),
                ]),
            ]);
    }

    public static function canAccess(): bool
    {
        return auth()->user()->role === Role::Admin->value;
    }
}
