<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'nim',
        'class',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function getNimNameAttribute()
    {
        return "{$this->nim} - {$this->name}";
    }
}
