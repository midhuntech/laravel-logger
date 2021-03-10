<?php

namespace midhuntech\LaravelLogger\App\Listeners;

use Illuminate\Auth\Events\Login;
use midhuntech\LaravelLogger\App\Http\Traits\ActivityLogger;

class LogSuccessfulLogin
{
    use ActivityLogger;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param Login $event
     *
     * @return void
     */
    public function handle(Login $event)
    {
        if (config('LaravelLogger.logSuccessfulLogin')) {
            ActivityLogger::activity('Logged In');
        }
    }
}
