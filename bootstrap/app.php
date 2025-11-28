<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->withSchedule(function (Schedule $schedule) {
        //  overdue rentals cron
        $schedule->call(function () {
            \App\Models\BookRental::overdue()
                ->whereNull('last_notified_at')
                ->chunkById(100, function ($rentals) {
                    foreach ($rentals as $rental) {
                        \App\Jobs\SendRentalOverdueNotification::dispatch($rental->id)
                            ->onQueue('mail')
                            ->afterCommit();
                    }
                });
        })->everyMinute()->name('check-overdue-rentals');
    })
    ->create();
