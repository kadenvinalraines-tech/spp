<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            try {
                $academicYears = \App\Models\AcademicYear::orderByDesc('start_date')->get();
                $activeYearId = \App\Models\AcademicYear::getActiveId();
                $activeYear = $academicYears->firstWhere('id', $activeYearId) ?? $academicYears->first();
                $view->with('globalAcademicYears', $academicYears);
                $view->with('globalActiveYear', $activeYear);
            } catch (\Exception $e) {
                $view->with('globalAcademicYears', collect());
                $view->with('globalActiveYear', null);
            }
        });

        Event::listen(\Illuminate\Auth\Events\Login::class, function ($event) {
            \App\Models\AuditTrail::create([
                'user_id'    => $event->user->id,
                'action'     => 'login',
                'model_type' => get_class($event->user),
                'model_id'   => $event->user->id,
                'ip_address' => \Illuminate\Support\Facades\Request::ip(),
                'user_agent' => \Illuminate\Support\Facades\Request::userAgent(),
                'created_at' => now(),
            ]);
        });

        Event::listen(\Illuminate\Auth\Events\Logout::class, function ($event) {
            if ($event->user) {
                \App\Models\AuditTrail::create([
                    'user_id'    => $event->user->id,
                    'action'     => 'logout',
                    'model_type' => get_class($event->user),
                    'model_id'   => $event->user->id,
                    'ip_address' => \Illuminate\Support\Facades\Request::ip(),
                    'user_agent' => \Illuminate\Support\Facades\Request::userAgent(),
                    'created_at' => now(),
                ]);
            }
        });
    }
}
