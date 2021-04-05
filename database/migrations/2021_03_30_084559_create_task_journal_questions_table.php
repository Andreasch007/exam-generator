<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaskJournalQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_journal_questions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('hdr_id');
            $table->integer('question_id');
            $table->integer('idx');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('task_journal_questions');
    }
}
