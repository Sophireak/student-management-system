<?php

namespace App\Helpers;

class AcademicCalendar
{
    /**
     * Cambodia Primary School Academic Year
     * October → September (12 months)
     * 
     * Semester 1: October – March (months 1-6)
     * Semester 2: April – September (months 7-12)
     */
    public static function months(): array
    {
        return [
            1  => ['kh' => 'តុលា',     'en' => 'October'],
            2  => ['kh' => 'វិច្ឆិកា',  'en' => 'November'],
            3  => ['kh' => 'ធ្នូ',     'en' => 'December'],
            4  => ['kh' => 'មករា',    'en' => 'January'],
            5  => ['kh' => 'កុម្ភៈ',   'en' => 'February'],
            6  => ['kh' => 'មីនា',    'en' => 'March'],
            7  => ['kh' => 'មេសា',   'en' => 'April'],
            8  => ['kh' => 'ឧសភា',  'en' => 'May'],
            9  => ['kh' => 'មិថុនា',  'en' => 'June'],
            10 => ['kh' => 'កក្កដា', 'en' => 'July'],
            11 => ['kh' => 'សីហា',   'en' => 'August'],
            12 => ['kh' => 'កញ្ញា',   'en' => 'September'],
        ];
    }

    /**
     * Get month name by number (1-12)
     */
    public static function monthName(int $month, string $lang = 'en'): string
    {
        return self::months()[$month][$lang] ?? '';
    }

    /**
     * Full label like "Month 1 — October (តុលា)"
     */
    public static function monthLabel(int $month): string
    {
        $data = self::months()[$month] ?? null;
        if (! $data) return '';
        return "Month {$month} — {$data['en']} ({$data['kh']})";
    }

    /**
     * Get months belonging to a semester
     * Semester 1: Oct–Mar (months 1-6)
     * Semester 2: Apr–Sep (months 7-12)
     */
    public static function semesterMonths(int $semester): array
    {
        return match($semester) {
            1 => [1, 2, 3, 4, 5, 6],
            2 => [7, 8, 9, 10, 11, 12],
            default => [],
        };
    }

    /**
     * Get semester label
     */
    public static function semesterLabel(int $semester, string $lang = 'en'): string
    {
        if ($lang === 'kh') {
            return $semester === 1
                ? 'ឆមាសទី ១ (តុលា – មីនា)'
                : 'ឆមាសទី ២ (មេសា – កញ្ញា)';
        }
        return $semester === 1
            ? 'Semester 1 (October – March)'
            : 'Semester 2 (April – September)';
    }

    /**
     * Total months in academic year
     */
    public static function totalMonths(): int
    {
        return count(self::months());
    }

    /**
     * Simple dropdown array
     * Returns: [1 => 'October', 2 => 'November', ..., 12 => 'September']
     */
    public static function monthDropdown(string $lang = 'en'): array
    {
        $result = [];
        foreach (self::months() as $num => $data) {
            $result[$num] = $data[$lang];
        }
        return $result;
    }
}