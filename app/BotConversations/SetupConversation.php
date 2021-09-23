<?php

namespace App\BotConversations;

use App\Models\CfpResult;
use BotMan\BotMan\Messages\Conversations\Conversation;
use Carbon\Carbon;

class SetupConversation extends Conversation
{
    /**
     * @inheritDoc
     */
    public function run(): void
    {
        $message = "*Welcome to the DeFiChain CFP Election Party*";
        $message .= sprintf(
            "\r\n\r\nCurrently the *%s voting round* is running until %s with %s active CFP.",
            config('cfp_settings.cfp_round'),
            Carbon::parse(config('cfp_settings.end_date'))->format('H:i d.m.Y'),
            CfpResult::count()
        );
        $message .= "\r\n\r\nThis bot informs you regularly about the current voting status.";

        $this->say($message, [
            'parse_mode' => 'Markdown',
        ]);
    }
}
