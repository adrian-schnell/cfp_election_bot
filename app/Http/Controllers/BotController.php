<?php

namespace App\Http\Controllers;

use App\BotConversations\SetupConversation;
use App\Models\Service\TelegramUserService;
use BotMan\BotMan\BotMan;

class BotController extends Controller
{
    public function handle(TelegramUserService $telegramUserService): void
    {
        /** @var BotMan $botman */
        $botman = app('botman');
        $botman->hears('/start', function (Botman $botman) use ($telegramUserService) {
            if ($telegramUserService->isNewUser($botman->getUser())) {
        ray($telegramUserService->getTelegramUser($botman->getUser()));
                $botman->startConversation(new SetupConversation($telegramUserService->getTelegramUser($botman->getUser())));
            }
        });

        $botman->listen();
    }
}
