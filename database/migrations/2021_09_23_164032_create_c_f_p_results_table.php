<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCFPResultsTable extends Migration
{
    public function up()
    {
        Schema::create('cfp_results', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->unsignedSmallInteger('github_issue_id');
            $table->unsignedSmallInteger('yes');
            $table->unsignedSmallInteger('no');
            $table->unsignedSmallInteger('neutral');
            $table->unsignedSmallInteger('votes_total');
            $table->unsignedSmallInteger('possible_votes');
            $table->unsignedFloat('vote_turnout', 5, 2);
            $table->string('current_result');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cfp_results');
    }
}
