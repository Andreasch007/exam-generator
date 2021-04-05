<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaskJournalExamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_journal_exams', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('exam_id')->unsigned();
            $table->date('doc_date');
            $table->time('start_time', $precision = 0);
            $table->integer('user_id')->unsigned();
            $table->integer('header_id')->unsigned();
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
        Schema::dropIfExists('task_journal_exams');
    }
}
