<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\ModelStates\HasStates;

class Attendance extends Model
{
    use HasFactory, HasStates;

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($attendance) {
            if (is_null($attendance->status)) {
                $attendance->status = \App\States\AttendanceStatus\Pending::$name;
            }
        });
    }

    /**
     * Scope a query to only include attendance for a specific lecturer.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGetAttendanceForLecturerById(Builder $query): Builder
    {
        return $query->where('attendable_type', '\App\Models\Lecturer')
            ->where('attendable_id', auth()->id());
    }

    /**
     * Relationship
     */
    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function lecturer()
    {
        return $this->belongsTo(User::class, 'attendable_id')->where('attendable_type', '\App\Models\Lecturer');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'attendable_id');
    }
}
