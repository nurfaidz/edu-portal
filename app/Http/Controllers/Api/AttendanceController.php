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

            return response()->apiSuccess(
                'Success',
                AttendanceJsonResource::collection($attendances)
            );
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

            $oddDateStart = date('Y') . '-02-01';
            $oddDateEnd = date('Y') . '-08-31';
            $evenDateStart = date('Y') . '-09-01';
            $evenDateEnd = date('Y') . '-12-31';
            $now = date('Y-m-d');

            $attendances = \App\Models\Attendance::whereHas('schedule', function ($query) use ($oddDateStart, $oddDateEnd, $evenDateStart, $evenDateEnd, $now) {
                if ($now >= $oddDateStart && $now <= $oddDateEnd) {
                    $query->whereRaw('MOD(semester, 2) <> 0')->where('academic_year', now()->year);
                } elseif ($now >= $evenDateStart && $now <= $evenDateEnd) {
                    $query->whereRaw('MOD(semester, 2) = 0')->where('academic_year', now()->year);
                }
            })
                ->where('expired_at', '<', now())
                ->where('attendable_id', $user->id)
                ->get();

            return response()->apiSuccess(
                'Success',
                AttendanceJsonResource::collection($attendances)
            );
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
                return response()->apiSuccess(
                    'Anda tidak terdaftar dalam jadwal ini',
                );
            } elseif (now() < $startDate) {
                return response()->apiSuccess(
                    'Anda belum bisa melakukan absen',
                );
            } elseif (now() > $attendance->expired_at) {
                $attendance->update([
                    'status' => Absent::$name
                ]);

                return response()->apiSuccess(
                    'Waktu absen sudah berakhir',
                );
            } elseif ($attendance->status === Present::$name) {
                return response()->apiSuccess(
                    'Anda sudah melakukan absen',
                );
            } else {
                $attendance->update([
                    'status' => Present::$name,
                ]);
            }

            return response()->apiSuccess(
                'Success',
                new AttendanceJsonResource($attendance)
            );

        } catch (\Exception $e) {
            return response()->apiError(
                500,
                $e->getMessage(),
            );
        }
    }
}
