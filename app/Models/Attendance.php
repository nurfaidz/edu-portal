<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\ModelStates\HasStates;

class Attendance extends Model
{
    use HasFactory, HasStates;

    protected $fillable = [
        'schedule_id',
        'lecturer_id',
        'student_id',
        'status',
    ];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function lecturer()
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
