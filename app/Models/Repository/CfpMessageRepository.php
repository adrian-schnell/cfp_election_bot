<?php

namespace App\Models\Repository;

use App\Models\CfpResult;
use Illuminate\Support\Collection;

class CfpMessageRepository
{
    public function getMessageFromCollection(Collection $cfpResults): string
    {
        $message = '';
        $cfpResults->each(function (CfpResult $cfpResult) use (&$message) {
            $message .= sprintf(
                "\r\n[%s](%s):\r\n%s\r\n(currently %s - %sx Yes, %sx No)\r\n\r\n",
                $cfpResult->title,
                $cfpResult->github_uri,
                voting_result_bar($cfpResult->yes, $cfpResult->no),
                $cfpResult->current_result === 'Approved' ? 'accepted ✅' : 'not accepted ❌',
                $cfpResult->yes,
                $cfpResult->no
            );
        });

        return $message;
    }
}
