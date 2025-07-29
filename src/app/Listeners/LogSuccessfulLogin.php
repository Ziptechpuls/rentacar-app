<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{
    public function handle(Login $event): void
    {
        if (method_exists($event->user, 'updateLastLoginAt')) {
            $event->user->updateLastLoginAt();
        }
    }
} 