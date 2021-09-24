<?php

namespace App\BotConversations;

use App\Models\CfpResult;
use App\Models\TelegramUser;
use BotMan\BotMan\Messages\Incoming\Answer;
use Illuminate\Support\Collection;
use Str;

class SelectCfpConversation extends \BotMan\BotMan\Messages\Conversations\Conversation
{
    public TelegramUser $telegramUser;

    public function __construct(TelegramUser $telegramUser)
    {
        $this->telegramUser = $telegramUser;
    }

    /**
     * @inheritDoc
     */
    public function run()
    {
        $this->bot->types();
        $this->say("🔎 Get only the results for CFP you\'re interested in. Pick the IDs listed below and send them comma separated. Invalid values will be stripped out.");
        $this->bot->typesAndWaits(1);
        $this->say($this->prepareCfpOverviewMessage(), ['parse_mode' => 'Markdown']);
        $this->ask('Enter your CFP wishlist (comma separated!), send `all` to receive all CFP results!:',
            function (Answer $answer) {
                $answerMessage = $answer->getMessage()->getText();
                if ($answerMessage === 'all') {
                    $this->telegramUser->update([
                        'cfp_selection' => [],
                    ]);
                    $this->say('You\'ll receive all CFP results from now');

                    return;
                }

                $answerToCfpIdArray = $this->answerToCfpIdArray($answerMessage);
                $this->telegramUser->update([
                    'cfp_selection' => $answerToCfpIdArray,
                ]);
                $this->say(sprintf('Your selection is set. You\'ll receive the CFP results for the %s selected CFPs.',
                    count($answerToCfpIdArray)));
            }, ['parse_mode' => 'Markdown']);
    }

    protected function prepareCfpOverviewMessage(): string
    {
        $cfps = CfpResult::orderBy('github_issue_id')->get();
        $message = '';
        $cfps->each(function (CfpResult $cfpResult) use (&$message) {
            $message .= sprintf("`ID %s`: [%s](%s)\r\n",
                $cfpResult->github_issue_id,
                $cfpResult->title,
                $cfpResult->github_uri);
        });

        return $message;
    }

    protected function answerToCfpIdArray(string $answerMessage): array
    {
        return Str::of($answerMessage)
            ->trim()
            ->split('/[\s,]+/')
            ->map(function (string $value, $key) {
                return (int)$value;
            })
            ->filter(function (int $value) {
                return CfpResult::where('github_issue_id', $value)->count() > 0;
            })
            ->unique()
            ->flatten()
            ->all();
    }
}
