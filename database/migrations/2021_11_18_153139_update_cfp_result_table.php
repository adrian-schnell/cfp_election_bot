<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCfpResultTable extends Migration
{
	public function up()
	{
		Schema::table('cfp_results', function (Blueprint $table) {
			$table->string('type')->default('cfp')->after('github_issue_id');
		});
	}

	public function down()
	{
		Schema::table('cfp_results', function (Blueprint $table) {
			$table->dropColumn('type');
		});
	}
}
