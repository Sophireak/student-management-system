<?php

namespace App\View\Components\Report;

use Illuminate\View\Component;
use Illuminate\View\View;

class ScoreGrid extends Component
{
    public function __construct(
        public \Illuminate\Support\Collection $enrollments,
        public \Illuminate\Support\Collection $subjects,
        public array $matrix,
        public float $maxScore,
        public bool $isLocked,
        public string $saveRoute,
        public array $hiddenFields = [],    // extra hidden inputs
        public bool $showRowAverage = false,
        public bool $showRank = false,
    ) {}

    public function render(): View
    {
        return view('components.report.score-grid');
    }
}