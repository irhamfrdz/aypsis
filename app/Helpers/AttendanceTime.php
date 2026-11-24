<?php

namespace App\Helpers;

use Carbon\Carbon;
use DateTimeInterface;

class AttendanceTime
{
    public static function normalize(string|DateTimeInterface $time, string $type): Carbon
    {
        $time = Carbon::parse($time);

        if (AttendanceEventType::normalize($type) === 'Masuk'
            && $time->hour === 9 && $time->minute >= 1 && $time->minute <= 3) {
            $time->setTime(9, 0, 0, 0);
        }

        return $time;
    }
}
