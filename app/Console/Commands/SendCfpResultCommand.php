<?php

namespace App\Console\Commands;

use App\Http\Service\TelegramMessageService;
use App\Models\CfpResult;
use App\Models\Repository\CfpMessageRepository;
use App\Models\Repository\TelegramUserRepository;
use Illuminate\Console\Command;

class SendCfpResultCommand extends Command
{
    protected $signature = 'cfp_result:send_updates';
    protected $description = 'Send the current results to the users';

    public function handle(
        TelegramUserRepository $repository,
        TelegramMessageService $messageService,
        CfpMessageRepository $messageRepository
    ): void {
        $cfpResults = CfpResult::orderBy('github_issue_id')->get();
        $recipients = $repository->getUsersForCurrentTime()->pluck('telegramId')->toArray();

        $message = $messageRepository->getMessageFromCollection($cfpResults);

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
