<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaskTransHeadersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_trans_headers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('exam_id')->unsigned();
            $table->date('doc_date');
            $table->datetime('start_time', $precision = 0);
            $table->datetime('end_time', $precision = 0);
            $table->integer('company_id');
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
        Schema::dropIfExists('task_trans_headers');
    }
}
