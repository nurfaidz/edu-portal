<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lecturer extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    /*
     * Relationship
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function courses()
    {
        return $this->user->lecturerCourses();
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'attendable_id', 'user_id')->where('attendable_type', '\App\Models\Lecturer');
    }

    public function lecturerCourses(): HasMany
    {
        return $this->hasMany(LecturerCourse::class, 'user_id', 'user_id');
    }

    public function schedules(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(Schedule::class, LecturerCourse::class, 'user_id', 'lecturer_course_id', 'user_id', 'id');
    }
}
