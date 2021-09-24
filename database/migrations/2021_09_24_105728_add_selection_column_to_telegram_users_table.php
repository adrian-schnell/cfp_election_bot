<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSelectionColumnToTelegramUsersTable extends Migration
{
	public function up()
	{
		Schema::table('telegram_users', function (Blueprint $table) {
			$table->json('cfp_selection')->default('[]')->after('result_qty');
		});
	}

	public function down()
	{
		Schema::table('telegram_users', function (Blueprint $table) {
			$table->dropColumn('cfp_selection');
		});
	}
}
