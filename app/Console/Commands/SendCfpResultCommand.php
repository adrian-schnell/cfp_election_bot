<?php

namespace App\Console\Commands;

use App\Http\Service\TelegramMessageService;
use App\Models\CfpResult;
use App\Models\Repository\TelegramUserRepository;
use Illuminate\Console\Command;

class SendCfpResultCommand extends Command
{
    protected $signature = 'cfp_result:send_updates';
    protected $description = 'Send the current results to the users';

    public function handle(TelegramUserRepository $repository, TelegramMessageService $messageService): void
    {
        $cfpResults = CfpResult::orderBy('github_issue_id')->get();
        $recipients = $repository->getUsersForCurrentTime()->pluck('telegramId')->toArray();

        $message = "";
        $cfpResults->each(function (CfpResult $cfpResult) use (&$message) {
            $message .= sprintf(
                "\r\n[%s](https://github.com/DeFiCh/dfips/issues/%s):\r\n%s\r\n(currently %s)\r\n\r\n",
                $cfpResult->title,
                $cfpResult->github_issue_id,
                voting_result_bar($cfpResult->yes, $cfpResult->no),
                $cfpResult->current_result === 'Approved' ? 'accepted ✅' : 'not accepted ❌'
            );
        });

        $messageService->sendMessage(
            $recipients,
            $message
        );
        $messageService->sendMessage(
            $recipients,
            sprintf(
                "*last update*: %s",
                $cfpResults->first()->updated_at->format('H:i - d.m.Y')
            ),
            ['parse_mode' => 'Markdown']
        );
    }
}
