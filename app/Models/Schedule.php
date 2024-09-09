<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Schedule extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $guarded = [];

    public function lecturerCourse()
    {
        return $this->belongsTo(LecturerCourse::class, 'lecturer_course_id', 'id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'schedule_id', 'id');
    }

    public function attendanceLecturer()
    {
        return $this->belongsTo(Attendance::class, 'id', 'schedule_id')->where('attendable_type', '\App\Models\Lecturer',)->where('attendable_id', auth()->id());
    }

    public function attendanceStudents()
    {
        return $this->hasMany(Attendance::class, 'schedule_id', 'id')->where('attendable_type', '\App\Models\Student');
    }

    /*
     * Scope
     */
    public function scopeReschedule($query)
    {
        return $query->where('is_reschedule', true);
    }
}
