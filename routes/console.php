<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Invoice;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    Invoice::whereIn('status', ['sent'])
        ->whereNotNull('due_date')
        ->where('due_date', '<', now()->startOfDay())
        ->update(['status' => 'overdue']);
})->dailyAt('00:05')->name('mark-overdue-invoices')->withoutOverlapping();
