<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Pagination\Paginator;
use App\View\Components\Admin\NavItem;
use App\View\Components\Admin\StatCard;
use App\Services\ReportService;
use App\Services\ScoreSheetService;
use App\Services\MonthlyReportService;
use App\Services\SemesterReportService;
use App\Services\AnnualReportService;
use App\View\Components\Report\ScoreGrid;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Use Tailwind pagination
        Paginator::useTailwind();

        Blade::component('admin.nav-item', NavItem::class);
        Blade::component('admin.stat-card', StatCard::class);
        Blade::component('report.score-grid', ScoreGrid::class);

        if (request()->header('X-Forwarded-Proto') === 'https') {
            \URL::forceScheme('https');
        }
    }

    public function register(): void
    {
        $this->app->singleton(ReportService::class);
        $this->app->singleton(ScoreSheetService::class);
        $this->app->singleton(MonthlyReportService::class);
        $this->app->singleton(SemesterReportService::class);
        $this->app->singleton(AnnualReportService::class);
    }
}