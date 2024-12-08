<?php

namespace App\Helpers;

use Illuminate\Support\Collection;
use Milon\Barcode\DNS1D;
use Milon\Barcode\DNS2D;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class Helper
{
    /**
     * Get the current semester based on now date
     *
     * @param Collection $lecturerCourses
     * @return Collection
     */
    public static function getCurrentSemester(Collection $lecturerCourses): Collection
    {
        // get odd date start : February - August
        $oddDateStart = date('Y') . '-02-01';
        $oddDateEnd = date('Y') . '-08-31';

        // get even date start : September - January
        $evenDateStart = date('Y') . '-09-01';
        $evenDateEnd = date('Y') . '-12-31';

        $now = date('Y-m-d');

        $isOddSemester = function ($semester) {
            return $semester % 2 !== 0;
        };

        if ($now >= $oddDateStart && $now <= $oddDateEnd) {
            return $lecturerCourses->filter(function ($course) use ($isOddSemester) {
                return $isOddSemester($course->semester);
            });
        } elseif ($now >= $evenDateStart && $now <= $evenDateEnd) {
            return $lecturerCourses->filter(function ($course) use ($isOddSemester) {
                return !$isOddSemester($course->semester);
            });
        }

        return $lecturerCourses;
    }

    /**
     * Generate QrCode for attendance Student
     *
     * @param string $scheduleId
     * @return string
     */
    public static function generateQrCode(string $scheduleId)
    {
        $barcode = new DNS2D();
        $generate = $barcode->getBarcodePNG($scheduleId, 'QRCODE', 30, 30);

        return $generate;
    }
}
