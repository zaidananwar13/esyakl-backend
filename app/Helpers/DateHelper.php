<?php

namespace App\Helpers;

use Illuminate\Support\Carbon;

class DateHelper
{
    public static function addDisplayOffset($date): Carbon
    {
        $offset = config('app-settings.timezone.display_offset_hours', 7);
        return Carbon::parse($date)->addHours($offset);
    }

    public static function formatDateTime($date): string
    {
        return self::addDisplayOffset($date)->format('d M Y - H:i');
    }

    public static function formatDate($date): string
    {
        return self::addDisplayOffset($date)->format('d M Y');
    }

    public static function formatDateLong($date): string
    {
        return Carbon::parse($date)->format('d F Y');
    }

    public static function formatTime($date): string
    {
        return Carbon::parse($date)->format('g:i A');
    }

    public static function nowTimestamp(): int
    {
        return (int) Carbon::now()->format('U');
    }

    public static function diffForHumans($datetime): string
    {
        return Carbon::parse($datetime)->diffForHumans();
    }

    public static function addDaysTimestamp(int $days): int
    {
        return (int) Carbon::now()->addDays($days)->format('U');
    }

    public static function addMonthsTimestamp(int $months): int
    {
        return (int) Carbon::now()->addMonths($months)->format('U');
    }

    public static function addYearsFormat($date, int $years, string $format = 'd F Y'): string
    {
        return Carbon::parse($date)->addYears($years)->format($format);
    }

    public static function expiryTimestamp(int $days): int
    {
        return (int) Carbon::now()->addDays($days)->format('U');
    }
}
