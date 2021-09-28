<?php

namespace App\Console\Commands;

use App\Models\CfpResult;
use App\Models\TelegramUser;
use Illuminate\Console\Command;

class ResetCfpVotingRoundCommand extends Command
{
    protected $signature = 'reset:cfp-round';
    protected $description = 'Reset the data of the last voting round';

    public function handle(): void
    {
        $choice = $this->choice('Are you sure to reset the voting round?', [
            'Yes - reset now',
            'Abort',
        ], 1);

        if ($choice === 'Abort') {
            $this->warn('Command aborted. No data was removed.');
            return;
        }
        // remove the old cfp
        CfpResult::truncate();
        $this->info('old CFP results removed');

        // reset the prefered CFP selection of all users
        TelegramUser::all()->each(function (TelegramUser $user) {
            $user->update([
                'cfp_selection' => [],
            ]);
        });
        $this->info('user CFP selection resetted');
    }
}
