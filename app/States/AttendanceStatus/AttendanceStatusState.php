<?php

namespace App\States\AttendanceStatus;

use App\States\AttendanceStatus\Present;
use App\States\AttendanceStatus\Absent;
use App\States\AttendanceStatus\Excused;
use Livewire\Wireable;
use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class AttendanceStatusState extends State implements Wireable
{
    abstract public function label(): string;

    abstract public function color(): string;

    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Absent::class)
            ->allowTransitions([
                [Absent::class, Present::class],
                [Absent::class, Excused::class],
            ]);
    }
}