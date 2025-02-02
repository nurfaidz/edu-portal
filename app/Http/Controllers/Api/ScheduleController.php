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

            $monthDay = date('m-d');
            $studentClass = $user->studentProfile->class;
            $studentCourses = $user->studentCourses;

            $startOfWeek = now()->startOfWeek();
            $endOfWeek = now()->endOfWeek();

            $schedules = \App\Models\Schedule::whereHas('lecturerCourse', function ($query) use ($studentCourses, $monthDay) {
                $query->whereRaw($monthDay >= '02-01' && $monthDay <= '08-31'
                    ? 'MOD(semester, 2) = 0'
                    : 'MOD(semester, 2) <> 0'
                )
                    ->where('academic_year', now()->year)
                    ->whereIn('course_id', $studentCourses->pluck('course_id'));
                $query->whereIn('course_id', $studentCourses->pluck('course_id'));
            })
                ->where('class', $studentClass)
                ->whereDate('date', '>=', $startOfWeek)
                ->whereDate('date', '<=', $endOfWeek)
                ->orderBy('date')
                ->get();

            return response()->apiSuccess(
                'Success',
                \App\Http\Resources\ScheduleJsonResource::collection($schedules)
            );
        } catch (\Exception $e) {
            return response()->apiError(
                500,
                $e->getMessage(),
            );
        }
    }
}
