<?php

namespace App\Enums;

enum ScoreType: string
{
    case Numeric  = 'numeric';   // Khmer, Math, Science, Social Studies
    case Grade    = 'grade';     // Moral Education
    case PassFail = 'pass_fail'; // Physical Education, Art, Music
}