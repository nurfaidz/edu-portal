<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AttendanceJsonResource;
use App\States\AttendanceStatus\Absent;
use App\States\AttendanceStatus\Present;
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

    public function store(Request $request)
    {
        try {
            $user = auth()->user();
            $schedule = \App\Models\Schedule::find($request->schedule_id);

            $attendance = \App\Models\Attendance::where('schedule_id', $schedule->id)
                ->where('attendable_id', $user->id)
                ->first();

            $date = \Carbon\Carbon::parse($schedule->date);
            $start = \Carbon\Carbon::parse($schedule->start);

            $startDate = $date->setTimeFrom($start)->subMinutes(10);

            if (!$attendance) {
                return response()->apiError(
                    400,
                    'Anda tidak terdaftar dalam jadwal ini',
                );
            } elseif (now() < $startDate) {
                return response()->apiError(
                    400,
                    'Anda belum bisa melakukan absen',
                );
            } elseif (now() > $attendance->expired_at) {
                $attendance->update([
                    'status' => Absent::$name
                ]);

                return response()->apiError(
                    200,
                    'Waktu absen sudah berakhir',
                    new AttendanceJsonResource($attendance),
                );
            } else {
                $attendance->update([
                    'status' => Present::$name,
                ]);
            }

            return response()->apiSuccess(new AttendanceJsonResource($attendance));

        } catch (\Exception $e) {
            return response()->apiError(
                500,
                $e->getMessage(),
            );
        }
    }
}
