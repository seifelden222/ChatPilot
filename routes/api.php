<?php

use App\Http\Controllers\FakeWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('webhooks/fake/{channel}', FakeWebhookController::class)
    ->name('webhooks.fake.ingest');
