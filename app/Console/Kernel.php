<?php

namespace App\Console;

use App\Console\Commands\ResetCfpVotingRoundCommand;
use App\Console\Commands\SendCfpResultCommand;
use App\Console\Commands\UpdateCfpResultCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
	    UpdateCfpResultCommand::class,
	    SendCfpResultCommand::class,
	    ResetCfpVotingRoundCommand::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('update:cfp_result')->everyTenMinutes()->withoutOverlapping();
        $schedule->command('cfp_result:send_updates')
            ->everyFifteenMinutes()
            ->withoutOverlapping();
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
