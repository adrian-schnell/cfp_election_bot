<?php

namespace App\Console\Commands;

use App\Client\CfpResultService;
use App\Models\CfpResult;
use Exception;
use Illuminate\Console\Command;
use Log;

class UpdateCfpResultCommand extends Command
{
	protected $signature = 'update:cfp_result';
	protected $description = 'Loads the current results';

	public function handle(CfpResultService $cfpResultService): void
	{
		try {
			$results = $cfpResultService->getAllCurrentCfp();
		} catch (Exception $e) {
			Log::error('cfp result update failed', [
				'message' => $e->getMessage(),
				'line'    => $e->getLine(),
				'file'    => $e->getFile(),
				'code'    => $e->getCode(),
			]);

			return;
		}

		foreach ($results as $result) {
			CfpResult::updateOrCreate([
				'github_issue_id' => $result['number'],
			], [
				'title'          => $result['title'],
				'type'           => $result['type'],
				'yes'            => $result['totalVotes']['yes'],
				'no'             => $result['totalVotes']['no'],
				'neutral'        => $result['totalVotes']['neutral'],
				'votes_total'    => $result['totalVotes']['total'],
				'possible_votes' => $result['totalVotes']['possible'],
				'vote_turnout'   => $result['totalVotes']['turnout'],
				'current_result' => $result['currentResult'],
			]);
		}
	}
}
