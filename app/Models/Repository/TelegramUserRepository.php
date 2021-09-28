<?php

namespace App\Models\Repository;

use App\Enum\ResultQtyEnum;
use App\Models\TelegramUser;
use Illuminate\Support\Collection;

class TelegramUserRepository
{
    public function getUsersForCurrentTime(): Collection
    {
        if (now() > config('cfp_settings.end_date')) {
            return TelegramUser::all();
        }

        if (now()->minute !== 0) {
            return TelegramUser::where('result_qty', ResultQtyEnum::MIN_15)->get();
        }

        if (now()->hour === 8) {
            return TelegramUser::whereIn('result_qty', [
                ResultQtyEnum::DAILY_1,
                ResultQtyEnum::HOURLY,
                ResultQtyEnum::DAILY_2,
                ResultQtyEnum::DAILY_3,
                ResultQtyEnum::MIN_15,
            ])->get();
        }

        if (now()->hour === 14) {
            return TelegramUser::whereIn('result_qty', [
                ResultQtyEnum::DAILY_3,
                ResultQtyEnum::HOURLY,
                ResultQtyEnum::MIN_15,
            ])->get();
        }

        if (now()->hour === 20) {
            return TelegramUser::whereIn('result_qty', [
                ResultQtyEnum::DAILY_3,
                ResultQtyEnum::HOURLY,
                ResultQtyEnum::DAILY_2,
                ResultQtyEnum::MIN_15,
            ])->get();
        }

        return TelegramUser::whereIn('result_qty', [
            ResultQtyEnum::HOURLY,
            ResultQtyEnum::MIN_15,
        ])->get();
    }
}
