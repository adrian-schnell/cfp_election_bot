<?php

namespace App\BotConversations;

use App\Models\CfpResult;
use App\Models\TelegramUser;
use BotMan\BotMan\Messages\Conversations\Conversation;
use Carbon\Carbon;

class SetupConversation extends Conversation
{
    public TelegramUser $telegramUser;

    public function __construct(TelegramUser $telegramUser)
    {
        $this->telegramUser = $telegramUser;
    }
    /**
     * @inheritDoc
     */
    public function run(): void
    {
        ray('here');
        $message = sprintf("🥳🥳🥳 *Welcome %s to the DeFiChain CFP Voting Bot* 🥳🥳🥳", $this->telegramUser->firstName);
        $message .= sprintf(
            "\r\n\r\nThe *%s voting round* is running until %s with %s active CFP.",
            config('cfp_settings.cfp_round'),
            Carbon::parse(config('cfp_settings.end_date'))->format('H:i d.m.Y'),
            CfpResult::count()
        );

        if (now()>config('cfp_settings.end_date')) {
            $message .= "\r\n\r\nThis round already ended. You'll receive the results in a second.. please take note: only the official results are valid. They will be posted on the GitHub page of each CFP soon.";
            $this->say($message, [
                'parse_mode' => 'Markdown',
            ]);
            $this->bot->typesAndWaits(3);
            $this->bot->startConversation(new ResultConversation());
            return;
        }

        $message .= "\r\n\r\nThis bot informs you regularly about the current voting status.";

        $this->say($message, [
            'parse_mode' => 'Markdown',
        ]);
    }
}
