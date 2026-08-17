<?php

namespace App\Helpers;

use Carbon\Carbon;

class KhmerDate
{
    /**
     * Khmer month names (Gregorian calendar)
     */
    protected static array $months = [
        1  => 'មករា',
        2  => 'កុម្ភៈ',
        3  => 'មីនា',
        4  => 'មេសា',
        5  => 'ឧសភា',
        6  => 'មិថុនា',
        7  => 'កក្កដា',
        8  => 'សីហា',
        9  => 'កញ្ញា',
        10 => 'តុលា',
        11 => 'វិច្ឆិកា',
        12 => 'ធ្នូ',
    ];

    /**
     * Khmer day names (0=Sunday .. 6=Saturday)
     */
    protected static array $days = [
        0 => 'អាទិត្យ',
        1 => 'ចន្ទ',
        2 => 'អង្គារ',
        3 => 'ពុធ',
        4 => 'ព្រហស្បតិ៍',
        5 => 'សុក្រ',
        6 => 'សៅរ៍',
    ];

    /**
     * Khmer digits 0-9
     */
    protected static array $digits = [
        '0' => '០', '1' => '១', '2' => '២', '3' => '៣', '4' => '៤',
        '5' => '៥', '6' => '៦', '7' => '៧', '8' => '៨', '9' => '៩',
    ];

    /**
     * Convert Arabic numerals to Khmer numerals
     */
    public static function toKhmerNumber(string|int $number): string
    {
        return strtr((string) $number, self::$digits);
    }

    /**
     * Get Khmer month name from month number (1-12)
     */
    public static function month(int $month): string
    {
        return self::$months[$month] ?? '';
    }

    /**
     * Get Khmer day name (0=Sunday, 6=Saturday)
     */
    public static function day(int $dayOfWeek): string
    {
        return self::$days[$dayOfWeek] ?? '';
    }

    /**
     * Full Khmer date: ថ្ងៃទី១១ ខែសីហា ឆ្នាំ២០២៦
     */
    public static function format(Carbon|string|null $date = null): string
    {
        $date = $date ? Carbon::parse($date) : Carbon::now();

        $day   = self::toKhmerNumber($date->format('d'));
        $month = self::month((int) $date->format('n'));
        $year  = self::toKhmerNumber($date->format('Y'));

        return "ថ្ងៃទី{$day} ខែ{$month} ឆ្នាំ{$year}";
    }

    /**
     * With day name: ថ្ងៃចន្ទ ទី១១ ខែសីហា ឆ្នាំ២០២៦
     */
    public static function formatWithDay(Carbon|string|null $date = null): string
    {
        $date = $date ? Carbon::parse($date) : Carbon::now();

        $dayName = self::day((int) $date->format('w'));
        $day     = self::toKhmerNumber($date->format('d'));
        $month   = self::month((int) $date->format('n'));
        $year    = self::toKhmerNumber($date->format('Y'));

        return "ថ្ងៃ{$dayName} ទី{$day} ខែ{$month} ឆ្នាំ{$year}";
    }
}