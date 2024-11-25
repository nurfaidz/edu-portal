<?php

namespace App\Http\Resources;

use App\States\AttendanceStatus\Pending;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceJsonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($this->status === Pending::$name) {
            $this->status = 'Tidak hadir';
        }
        return [
            'id' => $this->id,
            'lecturer' => $this->schedule->lecturerCourse->lecturer->lecturerProfile->name,
            'schedule_id' => $this->schedule->id,
            'schedule_date' => Carbon::parse($this->schedule->date)->locale('id_ID')->isoFormat('dddd, D MMMM Y'),
            'course' => $this->schedule->lecturerCourse->course->name,
            'attendable_type' => $this->attendable_type,
            'attendable_id' => $this->attendable_id,
            'status' => $this->status,
            'note' => $this->note,
            'checkin_at' => $this->checkin_at,
            'expired_at' => $this->expired_at,
            'created_at' => $this->created_at,
        ];
    }
}
