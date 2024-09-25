<?php

namespace App\Helpers;

use Illuminate\Support\Collection;

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


    /*
     * Get the current semester based on now date by lecturer
     *
     * @param Collection $query
     */
    public static function getCurrentSemesterCourses($query)
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
            return $query->filter(function ($course) use ($isOddSemester) {
                return $isOddSemester($course->semester);
            });
        } elseif ($now >= $evenDateStart && $now <= $evenDateEnd) {
            return $query->filter(function ($course) use ($isOddSemester) {
                return !$isOddSemester($course->semester);
            });
        }

        return $query;
    }
}
