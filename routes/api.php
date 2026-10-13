<?php

use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\TicketMessageController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->name('api.v1.')
    ->middleware(['source-system.api-key', 'throttle:source-system-api'])
    ->group(function (): void {
        Route::get('/tickets', [TicketController::class, 'index'])
            ->name('tickets.index');
        Route::post('/tickets', [TicketController::class, 'store'])
            ->name('tickets.store');
        Route::get('/tickets/{ticket}', [TicketController::class, 'show'])
            ->whereUuid('ticket')
            ->name('tickets.show');
        Route::get('/tickets/{ticket}/messages', [TicketMessageController::class, 'index'])
            ->whereUuid('ticket')
            ->name('tickets.messages.index');
        Route::post('/tickets/{ticket}/messages', [TicketMessageController::class, 'store'])
            ->whereUuid('ticket')
            ->name('tickets.messages.store');
    });
