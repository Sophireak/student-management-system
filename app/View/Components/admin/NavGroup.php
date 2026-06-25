<?php

namespace App\View\Components\Admin;

use Illuminate\View\Component;
use Illuminate\View\View;

class NavGroup extends Component
{
    public function __construct(
        public string $icon,
        public string $label,
        public array $routes = [],
    ) {}

    public function render(): View
    {
        return view('components.admin.nav-group');
    }

    public function isActiveGroup(): bool
    {
        foreach ($this->routes as $route) {
            if (request()->routeIs($route . '*')) {
                return true;
            }
        }
        return false;
    }
}