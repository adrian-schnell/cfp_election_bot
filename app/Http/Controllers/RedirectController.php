<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class RedirectController extends Controller
{
    public function redirectHome(): RedirectResponse
    {
        return redirect(config('telegram_bot.uri'));
    }
}
