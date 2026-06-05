<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\User;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


// About Schedule command 
// !! For Production Server (Live Environment)
// The command will run automatically every hour based on the schedule you set in 
// routes/console.php:

// Update no-show sessions every hour
Schedule::command('sessions:update-no-show')->hourly();

// This works without any user interaction - it runs on the server in the background. 

// !! For Local Development (Windows)
// Since we're on Windows with XAMPP, we need to keep the scheduler running. 
// we open a separate terminal/command prompt and run:

// php artisan schedule:work
// and we leave this terminal running. It will execute scheduled commands automatically 
// every minute (for testing) or hourly as configured.

// !! To Verify the Command Works:
// !! Run it manually once to test:

// php artisan sessions:update-no-show

// Alternative: Also we add Real-time Check in SessionController (Backup)
// Even if the scheduled command runs, we add this to 
// * SessionController@show  
// * SessionController@join  
// * SessionController@index 
// to check and update status when user visits


// check is Online for chat system
Schedule::call(function () {
    User::where('is_online', true)
        ->where('last_activity_at', '<', now()->subMinutes(5))
        ->update(['is_online' => false]);
})->everyMinute();

// for auto unlock text conversion for patient when time arrived
Schedule::command('unlock:text-sessions')->everyMinute();
