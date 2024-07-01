<?php namespace Pensoft\Restcoast\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateMeasureDefinitionsTable extends Migration
{
    public function up()
    {
        Schema::create('restcoast_measure_definitions', function ($table) {
            $table->increments('id');
            $table->string('name', 64);
            $table->string('code', 16)->unique();
            $table->mediumText('short_description')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('restcoast_measure_definitions');
    }
}
