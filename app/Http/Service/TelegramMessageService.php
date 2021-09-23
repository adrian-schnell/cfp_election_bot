<?php

namespace App\Http\Service;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\Exceptions\Base\BotManException;
use BotMan\Drivers\Telegram\TelegramDriver;
use Log;
use Str;

class TelegramMessageService
{
    protected BotMan $botman;

    public function __construct()
    {
        $this->botman = app('botman');
    }

    public function sendMessage(array $users, string $message, array $param = ['parse_mode' => 'Markdown']): bool
    {
        try {
            $this->botman->say(
                $this->escapeMessage($message),
                $users,
                TelegramDriver::class,
                $param
            );

            return true;
        } catch (BotManException $e) {
            Log::error('sending botman message failed', [
                'message'         => $e->getMessage(),
                'line'            => $e->getLine(),
                'message_to_user' => $message,
            ]);

            return false;
        }
    }

    protected function escapeMessage(string $message): string
    {
        return Str::replace('_', '\\_', $message);
    }
}
