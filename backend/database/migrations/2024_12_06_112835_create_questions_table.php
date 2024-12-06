<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionsTable extends Migration
{
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id('questionid');
            $table->string('subjectid');
            $table->unsignedBigInteger('teacherid');
            $table->text('question');
            $table->enum('level', ['EASY', 'NORMAL', 'HARD']);
            $table->string('rightanswer');
            $table->string('answer_a');
            $table->string('answer_b');
            $table->string('answer_c');
            $table->string('answer_d');
            $table->timestamps();
            
            $table->foreign('teacherid')->references('Teacherid')->on('teachers')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('questions');
    }
}
