<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AttendanceJsonResource;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function getAttendanceListToday()
    {
        try {
            $user = auth()->user();

            $studentCourses = $user->studentCourses;
            $lastestSemester = $studentCourses->where('academic_year', now()->year)->max('semester');

            $attendances = \App\Models\Attendance::whereHas('schedule', function ($query) use ($studentCourses, $lastestSemester) {
                $query->whereHas('lecturerCourse', function ($query) use ($studentCourses, $lastestSemester) {
                    $query->whereIn('course_id', $studentCourses->pluck('course_id'))
                        ->where('academic_year', now()->year)
                        ->where('semester', $lastestSemester);
                })->whereDate('date', now());
            })
                ->where('attendable_id', $user->id)
                ->get();

            return response()->apiSuccess(AttendanceJsonResource::collection($attendances));
        } catch (\Exception $e) {
            return response()->apiError(
                500,
                $e->getMessage(),
            );
        }
    }

    public function getAllAttendanceList()
    {
        try {
            $user = auth()->user();

            $studentCourses = $user->studentCourses;
            $lastestSemester = $studentCourses->where('academic_year', now()->year)->max('semester');

            $attendances = \App\Models\Attendance::whereHas('schedule', function ($query) use ($studentCourses, $lastestSemester) {
                $query->whereHas('lecturerCourse', function ($query) use ($studentCourses, $lastestSemester) {
                    $query->whereIn('course_id', $studentCourses->pluck('course_id'))
                        ->where('academic_year', now()->year)
                        ->where('semester', $lastestSemester);
                });
            })->where('attendable_id', $user->id)->get();

            return response()->apiSuccess(AttendanceJsonResource::collection($attendances));
        } catch (\Exception $e) {
            return response()->apiError(
                500,
                $e->getMessage(),
            );
        }
    }
}
