<?php

use App\Enum\ResultQtyEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTelegramUsersTable extends Migration
{
	public function up()
	{
		Schema::create('telegram_users', function (Blueprint $table) {
			$table->bigIncrements('id');
            $table->string('telegramId')->unique();
            $table->string('firstName')->nullable();
            $table->string('username')->nullable();
            $table->string('result_qty')->default(ResultQtyEnum::HOURLY);
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::dropIfExists('telegram_users');
	}
}
