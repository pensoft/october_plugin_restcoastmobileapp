<?php namespace Pensoft\Restcoast\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateThreatTable extends Migration
{
    public function up()
    {
        Schema::create('pensoft_restcoast_threats', function ($table) {
            $table->increments('id');
            $table->string('name', 64);
            $table->mediumText('short_description')->nullable();
            $table->text('description');
            $table->json('content_blocks')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pensoft_restcoast_threats');
    }
}
