<?php namespace Pensoft\Restcoast\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateThreatTable extends Migration
{
    public function up()
    {
        Schema::create('restcoast_threat_definitions', function ($table) {
            $table->increments('id');
            $table->string('name', 64);
            $table->string('code', 16)->unique();
            $table->mediumText('short_description')->nullable();
            $table->json('base_outcome')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('restcoast_threat_definitions');
    }
}
