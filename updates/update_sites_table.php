<?php namespace Pensoft\RestcoastMobileApp\Updates;

use October\Rain\Database\Updates\Migration;
use Schema;

class UpdateSitesTable extends Migration
{
    public function up()
    {
        Schema::table('rcm_sites', function ($table) {
            $table->json('country_codes')->nullable();
            $table->string('scale')->nullable();
            $table->json('image_gallery')->nullable();
            $table->json('stakeholders')->nullable();
        });
    }

    public function down()
    {
        Schema::table('rcm_sites', function ($table) {
            $table->dropColumn([
                'country_codes',
                'stakeholders',
                'scale',
                'image_gallery'
            ]);
        });

    }
}
