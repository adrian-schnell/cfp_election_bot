<?php

namespace App\Models\Repository;

use App\Models\CfpResult;
use Illuminate\Support\Collection;

class CfpMessageRepository
{
    public function getMessageFromCollection(Collection $cfpResults, array $cfpSelection): string
    {
        if (count($cfpSelection) === 0) {
            $cfpSelection = $cfpResults->pluck('github_issue_id')->toArray();
        }
        $message = '';
        $cfpResults->each(function (CfpResult $cfpResult) use (&$message, $cfpSelection) {
            if (!in_array($cfpResult->github_issue_id, $cfpSelection)) {
                return;
            }
            $message .= sprintf(
                "\r\n*#%s %s*: [%s](%s):\r\n%s\r\n(%s%s - %sx Yes, %sx No, %sx neutral)\r\n\r\n",
                $cfpResult->type,
                $cfpResult->github_issue_id,
                $cfpResult->title,
                $cfpResult->github_uri,
                voting_result_bar($cfpResult->yes, $cfpResult->no),
                now() < config('cfp_settings.end_date') ? 'currently ' : '',
                $cfpResult->current_result === 'Approved' ? 'accepted ✅' : 'not accepted ❌',
                $cfpResult->yes,
                $cfpResult->no,
                $cfpResult->neutral
            );
        });

        return $message;
    }
}
