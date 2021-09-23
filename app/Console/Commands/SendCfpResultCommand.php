<?php

namespace App\Console\Commands;

use App\Http\Service\TelegramMessageService;
use App\Models\CfpResult;
use App\Models\Repository\CfpMessageRepository;
use App\Models\Repository\TelegramUserRepository;
use App\Models\TelegramUser;
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
        if ($this->checkVotingStarted($messageService)
            || $this->checkVotingEnded($messageService)) {
            return;
        }

        $cfpResults = CfpResult::orderBy('github_issue_id')->get();
        $recipients = $repository->getUsersForCurrentTime()->pluck('telegramId')->toArray();
        if (count($recipients) === 0) {
            $this->info('no recipients selected');

            return;
        }

        $message = $messageRepository->getMessageFromCollection($cfpResults);

        $messageService->sendMessage(
            $recipients,
            $message
        );
        $messageService->sendMessage(
            $recipients,
            sprintf(
                "*last update*: %s",
                CfpResult::orderByDesc('updated_at')->first()->updated_at->format('H:i - d.m.Y')
            ),
            ['parse_mode' => 'Markdown']
        );
    }

    protected function checkVotingStarted(TelegramMessageService $messageService): bool
    {
        // inform users onetime after voting started
        $cacheKeyVotingStarted = sprintf('voting_%s_started', config('cfp_settings.cfp_round'));
        if (now() >= config('cfp_settings.start_date') && !cache($cacheKeyVotingStarted, false)) {
            $this->info('voting started info to all users');
            $votingStartedRecipients = TelegramUser::all()->pluck('telegramId')->toArray();
            $messageService->sendMessage(
                $votingStartedRecipients,
                sprintf("*Yehaa* - the new CFP Votings *%s* just started!", config('cfp_settings.cfp_round')),
                ['parse_mode' => 'Markdown']
            );
            cache([$cacheKeyVotingStarted => true]);

            return true;
        }

        return false;
    }

    protected function checkVotingEnded(TelegramMessageService $messageService): bool
    {
        // stop this command after the voting ended, inform users onetime
        if (now() > config('cfp_settings.end_date')) {
            $cacheKeyVotingEnded = sprintf('voting_%s_ended', config('cfp_settings.cfp_round'));
            if (!cache($cacheKeyVotingEnded, false)) {
                $votingStartedRecipients = TelegramUser::all()->pluck('telegramId')->toArray();

                $message = sprintf("*It's done...* \r\n\r\n the new CFP Votings *%s* just ended!",
                    config('cfp_settings.cfp_round'));
                $message .= "\r\n\r\n To see the result use the command /cfp_all";
                $messageService->sendMessage(
                    $votingStartedRecipients,
                    $message,
                    ['parse_mode' => 'Markdown']
                );
                cache([$cacheKeyVotingEnded => true]);
            }
            $this->info('voting ended info to all users');

            return true;
        }

        return false;
    }
}
