<?php namespace Pensoft\RestcoastMobileApp\Updates;

use October\Rain\Database\Updates\Migration;
use Schema;

class CreateSitesTable extends Migration
{
    public function up()
    {
        Schema::create('rcm_sites', function ($table) {
            $table->increments('id');
            $table->string('name', 64);
            $table->string('country', 64);
            $table->string('image')->nullable();
            $table->mediumText('short_description')->nullable();
            $table->string('gmap_objects_file')->nullable();
            $table->string('gmap_style_file')->nullable();
            $table->decimal('lat', 10, 6)->nullable();
            $table->decimal('long', 10, 6)->nullable();
            $table->json('content_blocks')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rcm_sites');
    }
}
