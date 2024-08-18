<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LecturerSalary extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'schedule_id',
        'amount',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function getAmountAttribute($value)
    {
        return 'Rp. ' . number_format($value, 0, ',', '.');
    }
}
