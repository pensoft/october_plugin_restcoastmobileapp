<?php namespace Pensoft\Restcoast\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateSitesTable extends Migration
{
    public function up()
    {
        Schema::create('pensoft_restcoast_sites', function ($table) {
            $table->increments('id');
            $table->string('name', 64);
            $table->mediumText('short_description')->nullable();
            $table->string('gmap_objects_file')->nullable();
            $table->string('gmap_style_file')->nullable();
            $table->json('content_blocks')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pensoft_restcoast_sites');
    }
}
