<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\Roles\Role;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, HasRoles, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'lecturer_name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getRoleAttribute()
    {
        return $this->getRoleNames()->first();
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    /**
     * Scope
     */

    public function scopeSuperadmin()
    {
        return $this->role(Role::Superadmin->value, 'superadmin');
    }

    // Admin
    public function scopeAdmin(){
        return $this->role(Role::Admin->value, 'admin');
    }

    // Lecturer
    public function scopeLecturer()
    {
        return $this->role(Role::Lecturer->value, 'lecturer');
    }

    // Student
    public function scopeStudent()
    {
        return $this->role(Role::Student->value, 'student');
    }

    /**
     * Relationship of lecturer
     */
    public function lecturerProfile()
    {
        return $this->hasOne(Lecturer::class);
    }

    public function lecturerCourses()
    {
        return $this->hasMany(LecturerCourse::class, 'user_id');
    }

    public function schedules()
    {
        return $this->hasManyThrough(Schedule::class, LecturerCourse::class, 'user_id', 'lecturer_course_id');
    }

    /**
     * Relationship of student
     */
    public function studentProfile()
    {
        return $this->hasOne(Student::class);
    }

    public function studentCourses()
    {
        return $this->hasMany(StudentCourse::class, 'user_id');
    }
}
