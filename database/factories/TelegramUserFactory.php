<?php

namespace Database\Factories;

use App\Enum\ResultQtyEnum;
use App\Models\TelegramUser;
use Arr;
use Illuminate\Database\Eloquent\Factories\Factory;

class TelegramUserFactory extends Factory
{
    protected $model = TelegramUser::class;

    public function definition(): array
    {
        return [
            'telegramId' => $this->faker->unique()->word,
            'firstName'  => $this->faker->unique()->firstName,
            'username'   => $this->faker->userName,
            'result_qty' => Arr::random([
                ResultQtyEnum::HOURLY,
                ResultQtyEnum::MIN_15,
                ResultQtyEnum::DAILY_1,
                ResultQtyEnum::DAILY_2,
                ResultQtyEnum::DAILY_3,
                ResultQtyEnum::ON_DEMAND,
            ], 1)[0],
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function resultQty(string $value): TelegramUserFactory
    {
        return $this->state(function (array $attributes) use ($value) {
            return [
                'result_qty' => $value,
            ];
        });
    }
}
