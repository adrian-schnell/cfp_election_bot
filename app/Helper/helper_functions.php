<?php
if (!function_exists('voting_result_bar')) {
    function voting_result_bar(float $yesVotes, float $noVotes): string
    {
        $progress = round($yesVotes / ($yesVotes + $noVotes), 2)*100;

        return sprintf(
            '\[%s%s] %s',
            str_repeat('Y', $progress / 4),
            str_repeat('n', (100 - $progress) / 4),
            $progress . '% agree'
        );
    }
}
