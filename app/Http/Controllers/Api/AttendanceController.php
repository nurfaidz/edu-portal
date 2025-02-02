<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AttendanceJsonResource;
use App\States\AttendanceStatus\Absent;
use App\States\AttendanceStatus\Present;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function getAttendanceListToday()
    {
        try {
            $user = auth()->user();

            $monthDay = date('m-d');

            $attendances = \App\Models\Attendance::whereHas('schedule', function ($query) use ($monthDay) {
                $query->whereRaw($monthDay >= '02-01' && $monthDay <= '08-31'
                    ? 'MOD(semester, 2) = 0'
                    : 'MOD(semester, 2) <> 0'
                )
                    ->where('academic_year', now()->year);
            })
                ->whereDate('expired_at', '<=', now())
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

            $monthDay = date('m-d');
            $now = now();

            $attendances = \App\Models\Attendance::whereHas('schedule', function ($query) use ($monthDay) {
                if ($monthDay >= '02-01' && $monthDay <= '08-31') {
                    $query->whereRaw('MOD(semester, 2) <> 0')->where('academic_year', now()->year);
                } else {
                    $query->whereRaw('MOD(semester, 2) = 0')->where('academic_year', now()->year);
                }
            })
                ->whereDate('expired_at', '<=', $now)
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
