<?php
if (!function_exists('voting_result_bar')) {
    function voting_result_bar(float $yesVotes, float $noVotes): string
    {
		if ($yesVotes == 0 && $noVotes == 0) {
			$progress = 0;
		} else {
			$progress = round($yesVotes / ($yesVotes + $noVotes), 2) * 100;
		}

        return sprintf(
            '%s%s %s',
            str_repeat('🟩', $progress / 8),
            str_repeat('🟥', (100 - $progress) / 8),
            $progress . '% agree'
        );
    }
}
