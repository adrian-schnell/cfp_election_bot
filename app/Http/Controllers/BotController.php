<?php

namespace App\Http\Controllers;

use App\BotConversations\ResultConversation;
use App\BotConversations\SettingsConversation;
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
                $telegramUser = $telegramUserService->getTelegramUser($botman->getUser());
                $botman->startConversation(new SetupConversation($telegramUser));
            }
            $botman->startConversation(new SettingsConversation($telegramUserService->getTelegramUser($botman->getUser())));
        });
        $botman->hears('/settings', function (Botman $botman) use ($telegramUserService) {
            $botman->startConversation(new SettingsConversation($telegramUserService->getTelegramUser($botman->getUser())));
        });

        $botman->hears('/cfp ([0-9]+)', function (Botman $botman, $cfpGithubId) use ($telegramUserService) {
            $botman->startConversation(new ResultConversation($cfpGithubId));
        });
        $botman->hears('/cfp_all', function (Botman $botman) use ($telegramUserService) {
            $botman->startConversation(new ResultConversation());
        });

        $botman->listen();
    }
}
