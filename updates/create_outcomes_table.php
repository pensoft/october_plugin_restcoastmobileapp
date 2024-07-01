<?php namespace Pensoft\Restcoast\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateOutcomesTable extends Migration
{
    public function up()
    {
        Schema::create('restcoast_outcomes', function ($table) {
            $table->increments('id');
            $table->string('name', 64);
            $table->integer('environmental_score');
            $table->integer('economic_score');
            $table->json('content_blocks')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('restcoast_outcomes');
    }
}
