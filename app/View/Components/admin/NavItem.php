<?php

namespace App\View\Components\Admin;

use Illuminate\View\Component;
use Illuminate\View\View;

class NavItem extends Component
{
    public function __construct(
        public string $route,
        public string $icon,
        public string $label,
    ) {}

    public function render(): View
    {
        return view('components.admin.nav-item');
    }

    public function isActive(): bool
    {
        return request()->routeIs($this->route . '*');
    }
}
