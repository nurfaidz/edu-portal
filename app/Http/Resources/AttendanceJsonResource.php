<?php

namespace App\Http\Resources;

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
        return [
            'id' => $this->id,
            'schedule_id' => $this->schedule->id,
            'attendable_type' => $this->attendable_type,
            'attendable_id' => $this->student->id,
            'status' => $this->status,
            'note' => $this->note,
            'checkin_at' => $this->checkin_at,
            'expired_at' => $this->expired_at,
            'created_at' => $this->created_at,
        ];
    }
}
