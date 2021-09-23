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
                'messsage' => $e->getMessage(),
                'line'     => $e->getLine(),
                'file'     => $e->getFile(),
                'code'     => $e->getCode(),
            ]);
            return;
        }

        foreach ($results as $result) {
            CfpResult::updateOrCreate([
                'github_issue_id' => $result['number'],
            ], [
                'title'          => $result['title'],
                'yes'            => $result['yes'],
                'no'             => $result['no'],
                'neutral'        => $result['neutral'],
                'votes_total'    => $result['votes'],
                'possible_votes' => $result['possibleVotes'],
                'vote_turnout'   => $result['voteTurnout'],
                'current_result' => $result['currentResult'],
            ]);
        }
    }
}
