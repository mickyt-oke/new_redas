<?php

use App\Models\AuthToken;
use App\Models\OtpCode;
use App\Models\PersonalAccessToken;
use App\Models\RefreshToken;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('model:prune', [
    '--model' => [
        AuthToken::class,
        OtpCode::class,
        PersonalAccessToken::class,
        RefreshToken::class,
    ],
])->dailyAt('02:17');
