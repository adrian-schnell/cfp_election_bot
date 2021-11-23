<?php

namespace App\Console\Commands;

use App\Http\Service\TelegramMessageService;
use App\Models\CfpResult;
use App\Models\Repository\CfpMessageRepository;
use App\Models\Repository\TelegramUserRepository;
use App\Models\TelegramUser;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendCfpResultCommand extends Command
{
	protected $signature = 'cfp_result:send_updates';
	protected $description = 'Send the current results to the users';

	public function handle(
		TelegramUserRepository $repository,
		TelegramMessageService $messageService,
		CfpMessageRepository   $messageRepository
	): void {
		if (!$this->checkVotingStarted($messageService)
			|| $this->checkVotingEnded($messageService)) {
			return;
		}

		$cfpResults = CfpResult::orderBy('github_issue_id')->get();
		$recipients = $repository->getUsersForCurrentTime();
		if (count($recipients) === 0) {
			$this->info('no recipients selected');

			return;
		}

		// send cfp results for each user, depending on it's CFP selection
		foreach ($recipients as $recipient) {
			/** @var TelegramUser $recipient */
			$message = $messageRepository->getMessageFromCollection($cfpResults, $recipient->cfp_selection);
			$messageService->sendMessage(
				[$recipient->telegramId],
				$message,
				['disable_web_page_preview' => true, 'parse_mode' => 'Markdown']
			);
		}
	}

	protected function checkVotingStarted(TelegramMessageService $messageService): bool
	{
		// inform users onetime after voting started
		$cacheKeyVotingStarted = sprintf('voting_%s_started', config('cfp_settings.cfp_round'));
		$startDate = config('cfp_settings.start_date');
		if (now() >= $startDate) {

			if (!cache($cacheKeyVotingStarted, false) && now()->diffInMinutes(Carbon::parse($startDate)) < 30) {
				$this->info('voting started info to all users');
				$votingStartedRecipients = TelegramUser::all()->pluck('telegramId')->toArray();
				$messageService->sendMessage(
					$votingStartedRecipients,
					sprintf("*Yehaa* - the new CFP Votings *%s* just started!\r\n\r\nSelect the CFP/DFIP you want to monitor with the command /settings",
						config
						('cfp_settings.cfp_round')),
					['parse_mode' => 'Markdown']
				);
				cache([$cacheKeyVotingStarted => true]);
			}

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

				return false;
			}
			$this->info('voting ended info to all users');

			return true;
		}

		return false;
	}
}
