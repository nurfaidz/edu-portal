<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleJsonResource extends JsonResource
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
            'lecturer' => $this->lecturerCourse->lecturer->lecturerProfile->name,
            'course' => $this->lecturerCourse->course->name,
            'classroom' => $this->classroom,
            'date' => Carbon::parse($this->date)->locale('id_ID')->isoFormat('dddd'),
            'start' => $this->start,
            'end' => $this->end,
            'extras' => $this->extras,
            'is_reschedule' => $this->is_reschedule,
            'semester' => $this->semester,
            'academic_year' => $this->academic_year,
        ];
    }
}
