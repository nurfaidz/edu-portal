<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\AccountWidget as WidgetsAccountWidget;

class AccountWidget extends WidgetsAccountWidget
{
    /**
     * @var view-string
     */
    protected static string $view = 'account-widget';
}
