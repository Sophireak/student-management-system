<?php

namespace App\Helpers;

class ScoreHelper
{
    public static function grade(?float $score): array
    {
        if ($score === null) {
            return ['label' => '—', 'kh' => '—', 'color' => 'gray'];
        }

        return match(true) {
            $score >= 9.00 => ['label' => 'Excellent', 'kh' => 'ល្អណាស់',   'color' => 'green'],
            $score >= 8.00 => ['label' => 'Very Good', 'kh' => 'ល្អ',        'color' => 'blue'],
            $score >= 7.00 => ['label' => 'Good',      'kh' => 'ល្អបង្គួរ',  'color' => 'orange'],
            $score >= 6.00 => ['label' => 'Average',   'kh' => 'មធ្យម',      'color' => 'yellow'],
            $score >= 5.00 => ['label' => 'Weak',      'kh' => 'ខ្សោយ',      'color' => 'amber'],
            default        => ['label' => 'Fail',      'kh' => 'ធ្លាក់',      'color' => 'red'],
        };
    }
}