<?php

namespace App\BotConversations;

use App\Models\CfpResult;
use App\Models\TelegramUser;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use Str;

class SelectCfpConversation extends Conversation
{
    public TelegramUser $telegramUser;
    public bool $setupMode;

    public function __construct(TelegramUser $telegramUser, bool $setupMode = false)
    {
        $this->telegramUser = $telegramUser;
        $this->setupMode = $setupMode;
    }

    /**
     * @inheritDoc
     */
    public function run()
    {
        $this->bot->types();
        $this->say("🔎 Get only the results for CFP you're interested in. Pick the IDs listed below and send them comma separated. Invalid values will be stripped out.");
        $this->bot->typesAndWaits(1);
        $this->say($this->prepareCfpOverviewMessage(),
            ['parse_mode' => 'Markdown', 'disable_web_page_preview' => true]);
        $this->ask('Enter your CFP wishlist (comma separated!), send `all` to receive all CFP results!:',
            function (Answer $answer) {
                $answerMessage = $answer->getMessage()->getText();
                if ($answerMessage === 'all') {
                    $this->telegramUser->update([
                        'cfp_selection' => [],
                    ]);
                    $this->say('You\'ll receive all CFP results from now');

                    // only in setup mode!
                    if ($this->setupMode) {
                        $this->setupMessages();
                    }

                    return;
                }

                $answerToCfpIdArray = $this->answerToCfpIdArray($answerMessage);
                $this->telegramUser->update([
                    'cfp_selection' => $answerToCfpIdArray,
                ]);
                $this->say(sprintf('Your selection is set. You\'ll receive the CFP results for the %s selected CFPs.',
                    count($answerToCfpIdArray)));

                // only in setup mode!
                if ($this->setupMode) {
                    $this->setupMessages();
                }
            }, ['parse_mode' => 'Markdown']);

    }

    protected function setupMessages()
    {
        $this->say('Your `CFP Voting` bot is setup now. Enjoy the show. 🥳', ['parse_mode' => 'Markdown']);
        $this->say('Get on demand a specific CFP with `/cfp GITHUB_ID` or all with `/cfp_all`',
            ['parse_mode' => 'Markdown']);
        $this->say('_Hint_: you can change this setting with the `/settings` command', [
            'parse_mode' => 'Markdown',
        ]);
        $this->bot->typesAndWaits(2);
        $this->say("Brought you by [DFI Signal](https://dfi-signal.com) & API Services by DFX ❤️\r\n\r\nCheck a visual overview on [Masternode Monitor](https://next.defichain-masternode-monitor.com//#/votings)",
            [
                'parse_mode'               => 'Markdown',
                'disable_web_page_preview' => true,
            ]);
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
