<?php

namespace App\View\Components\Admin;

use Illuminate\View\Component;
use Illuminate\View\View;

class StatCard extends Component
{
    public function __construct(
        public string $label,
        public string|int $value,
        public string $icon,
        public string $color = 'blue',
    ) {}

    public function render(): View
    {
        return view('components.admin.stat-card');
    }
}
