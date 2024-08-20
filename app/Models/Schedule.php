<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function lecturerCourse()
    {
        return $this->belongsTo(LecturerCourse::class, 'lecturer_course_id', 'id');
    }

    public static function getScheduleByLecturerId($lecturerId)
    {
        return self::whereHas('lecturerCourse', function ($query) use ($lecturerId) {
            $query->where('user_id', $lecturerId);
        })
        ->where('date', now()->format('Y-m-d'))
        ->get();
    }
}
