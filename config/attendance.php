<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Attendance Session Times
    |--------------------------------------------------------------------------
    | Cambodia public school standard hours
    */

    'sessions' => [
        'morning' => [
            'start'    => '07:00',
            'end'      => '11:00',
            'lock_at'  => '11:00', // auto-lock time
            'label'    => 'ព្រឹក', // Khmer: Morning
            'label_en' => 'Morning',
        ],
        'afternoon' => [
            'start'    => '13:00',
            'end'      => '17:00',
            'lock_at'  => '17:00', // auto-lock time
            'label'    => 'រសៀល', // Khmer: Afternoon
            'label_en' => 'Afternoon',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Auto-Present Default
    |--------------------------------------------------------------------------
    | If true, all students are marked "present" by default
    | Teacher only needs to change students who are absent/late/excused
    */
    'auto_present' => true,

    /*
    |--------------------------------------------------------------------------
    | Retroactive Editing
    |--------------------------------------------------------------------------
    | If true, teachers can edit past days
    | If false, past days are locked for teachers (admin can always edit)
    */
    'allow_retroactive_teacher' => false,
];