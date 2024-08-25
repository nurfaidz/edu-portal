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

    public function attendanceLecturer()
    {
        return $this->belongsTo(Attendance::class, 'id', 'schedule_id')->where('attendable_type', '\App\Models\Lecturer',)->where('attendable_id', auth()->id());
    }

    public function attendanceStudents()
    {
        return $this->hasMany(Attendance::class, 'schedule_id', 'id')->where('attendable_type', '\App\Models\Student');
    }
}
