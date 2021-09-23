<?php

namespace App\BotConversations;

use App\Enum\ResultQtyEnum;
use App\Models\CfpResult;
use App\Models\TelegramUser;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;
use Carbon\Carbon;

class SettingsConversation extends Conversation
{
    public TelegramUser $telegramUser;

    public function __construct(TelegramUser $telegramUser)
    {
        $this->telegramUser = $telegramUser;
    }

    /**
     * @inheritDoc
     */
    public function run(): void
    {
        $question = Question::create('How often would you like to receive the latest results?')
            ->addButtons([
                Button::create('every 15min')->value(ResultQtyEnum::MIN_15),
                Button::create('hourly')->value(ResultQtyEnum::HOURLY),
                Button::create('morning, noon, evening')->value(ResultQtyEnum::DAILY_3),
                Button::create('morning, evening')->value(ResultQtyEnum::DAILY_2),
                Button::create('morning')->value(ResultQtyEnum::DAILY_1),
            ]);

        $this->ask($question, function (Answer $answer) {
            if (!$answer->isInteractiveMessageReply()) {
                $this->repeat('please select an option above');

                return;
            }

            $this->telegramUser->update([
                'result_qty' => $answer->getValue(),
            ]);
            $this->say('Your CFP election party is setup now. Enjoy the show. 🥳');
        });
    }
}
