<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function getWeeklySchedule()
    {
        try {
            $user = auth()->user();

            $studentClass = $user->studentProfile->class;
            $studentCourses = $user->studentCourses;
            $lastestSemester = $studentCourses->where('academic_year', now()->year)->max('semester');

            $startOfWeek = now()->startOfWeek();
            $endOfWeek = now()->endOfWeek();

            $schedules = \App\Models\Schedule::whereHas('lecturerCourse', function ($query) use ($studentCourses, $lastestSemester) {
                $query->whereIn('course_id', $studentCourses->pluck('course_id'))
                    ->where('academic_year', now()->year)
                    ->where('semester', $lastestSemester);
            })
                ->where('class', $studentClass)
                ->whereBetween('date', [$startOfWeek, $endOfWeek])
                ->orderBy('date')
                ->get();

            return response()->apiSuccess(\App\Http\Resources\ScheduleJsonResource::collection($schedules));
        } catch (\Exception $e) {
            return response()->apiError(
                500,
                $e->getMessage(),
            );
        }
    }
}
