<?php

namespace App\BotConversations;

use App\Enum\ResultQtyEnum;
use App\Models\CfpResult;
use App\Models\Repository\CfpMessageRepository;
use App\Models\TelegramUser;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;
use Carbon\Carbon;
use Exception;

class ResultConversation extends Conversation
{
    public ?int $cfpGithubId;

    public function __construct(?int $cfpGithubId = null)
    {
        $this->cfpGithubId = $cfpGithubId;
    }

    /**
     * @inheritDoc
     */
    public function run(): void
    {
        if (is_null($this->cfpGithubId)) {
            $cfp = CfpResult::all();
        } else {
            $cfp = CfpResult::where('github_issue_id', $this->cfpGithubId)->limit(1)->get();
        }

        if ($cfp->count() === 0) {
            $this->say(sprintf("uuups... The CFP with the ID *%s* does not exist..", $this->cfpGithubId), [
                'parse_mode' => 'Markdown',
            ]);

            return;
        }
        $this->say(
            app(CfpMessageRepository::class)->getMessageFromCollection($cfp),
            [
                'parse_mode' => 'Markdown',
            ]
        );
    }
}
