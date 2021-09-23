<?php

namespace Tests\Unit;

use App\Enum\ResultQtyEnum;
use App\Models\Repository\TelegramUserRepository;
use App\Models\TelegramUser;
use Carbon\Carbon;
use Tests\TestCase;

class TelegramUserRepositoryTest extends TestCase
{
    public function test_15_min(): void
    {
        $telegramUserRepo = app(TelegramUserRepository::class);
        TelegramUser::factory()->resultQty(ResultQtyEnum::MIN_15)->count(3)->create();
        TelegramUser::factory()->resultQty(ResultQtyEnum::DAILY_1)->count(3)->create();
        TelegramUser::factory()->resultQty(ResultQtyEnum::DAILY_2)->count(3)->create();
        TelegramUser::factory()->resultQty(ResultQtyEnum::DAILY_3)->count(3)->create();
        TelegramUser::factory()->resultQty(ResultQtyEnum::HOURLY)->count(3)->create();
        $this->travelTo(Carbon::parse('2021-11-11 00:01:00'));
        $this->assertEquals(3, $telegramUserRepo->getUsersForCurrentTime()->count());

        $this->travelTo(Carbon::parse('2021-11-11 00:15:00'));
        $this->assertEquals(3, $telegramUserRepo->getUsersForCurrentTime()->count());

        $this->travelTo(Carbon::parse('2021-11-11 15:30:00'));
        $this->assertEquals(3, $telegramUserRepo->getUsersForCurrentTime()->count());

        $this->travelBack();
    }

    public function test_daily_once(): void
    {
        $telegramUserRepo = app(TelegramUserRepository::class);
        TelegramUser::factory()->resultQty(ResultQtyEnum::MIN_15)->count(3)->create();
        TelegramUser::factory()->resultQty(ResultQtyEnum::DAILY_1)->count(3)->create();
        TelegramUser::factory()->resultQty(ResultQtyEnum::DAILY_2)->count(3)->create();
        TelegramUser::factory()->resultQty(ResultQtyEnum::DAILY_3)->count(3)->create();
        TelegramUser::factory()->resultQty(ResultQtyEnum::HOURLY)->count(3)->create();
        $this->travelTo(Carbon::parse('2021-11-11 7:59:00'));
        $this->assertEquals(3, $telegramUserRepo->getUsersForCurrentTime()->count());

        $this->travelTo(Carbon::parse('2021-11-11 8:00:15'));
        $this->assertEquals(15, $telegramUserRepo->getUsersForCurrentTime()->count());

        $this->travelTo(Carbon::parse('2021-11-11 8:02:00'));
        $this->assertEquals(3, $telegramUserRepo->getUsersForCurrentTime()->count());

        $this->travelBack();
    }

    public function test_daily_twice(): void
    {
        $telegramUserRepo = app(TelegramUserRepository::class);
        TelegramUser::factory()->resultQty(ResultQtyEnum::DAILY_1)->count(3)->create();
        TelegramUser::factory()->resultQty(ResultQtyEnum::DAILY_2)->count(3)->create();

        $this->travelTo(Carbon::parse('2021-11-11 8:00:15'));
        $this->assertEquals(6, $telegramUserRepo->getUsersForCurrentTime()->count());

        $this->travelTo(Carbon::parse('2021-11-11 14:00:00'));
        $this->assertEquals(0, $telegramUserRepo->getUsersForCurrentTime()->count());

        $this->travelTo(Carbon::parse('2021-11-11 20:00:00'));
        $this->assertEquals(3, $telegramUserRepo->getUsersForCurrentTime()->count());

        $this->travelBack();
    }

    public function test_daily_three_times(): void
    {
        $telegramUserRepo = app(TelegramUserRepository::class);
        TelegramUser::factory()->resultQty(ResultQtyEnum::DAILY_1)->count(3)->create();
        TelegramUser::factory()->resultQty(ResultQtyEnum::DAILY_2)->count(3)->create();
        TelegramUser::factory()->resultQty(ResultQtyEnum::DAILY_3)->count(3)->create();

        $this->travelTo(Carbon::parse('2021-11-11 8:00:15'));
        $this->assertEquals(9, $telegramUserRepo->getUsersForCurrentTime()->count());

        $this->travelTo(Carbon::parse('2021-11-11 14:00:00'));
        $this->assertEquals(3, $telegramUserRepo->getUsersForCurrentTime()->count());

        $this->travelTo(Carbon::parse('2021-11-11 20:00:00'));
        $this->assertEquals(6, $telegramUserRepo->getUsersForCurrentTime()->count());

        $this->travelBack();
    }

    public function test_hourly(): void
    {
        $telegramUserRepo = app(TelegramUserRepository::class);
        TelegramUser::factory()->resultQty(ResultQtyEnum::HOURLY)->count(3)->create();

        $this->travelTo(Carbon::parse('2021-11-11 8:00:15'));
        $this->assertEquals(3, $telegramUserRepo->getUsersForCurrentTime()->count());

        $this->travelTo(Carbon::parse('2021-11-11 8:02:00'));
        $this->assertEquals(0, $telegramUserRepo->getUsersForCurrentTime()->count());

        $this->travelTo(Carbon::parse('2021-11-11 11:00:00'));
        $this->assertEquals(3, $telegramUserRepo->getUsersForCurrentTime()->count());

        $this->travelBack();
    }
}
