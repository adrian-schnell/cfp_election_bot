<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;

class BotController extends Controller
{
    public function handle(): void
    {
        /** @var BotMan $botman */
        $botman = app('botman');
    }
}
