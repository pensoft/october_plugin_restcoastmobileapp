<?php

namespace Pensoft\RestcoastMobileApp\Updates;

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

class AddSitesHomeData extends Migration
{
    public function up()
    {
        Schema::table('rcm_sites', function (Blueprint $table) {
            $table->string("coordinates_lat")->nullable();
            $table->string("coordinates_lon")->nullable();
            $table->string("location")->nullable();
            $table->json("country_codes")->nullable();
            $table->string("scale")->nullable();
            $table->json("image_gallery")->nullable();
        });
    }

    public function down()
    {
        Schema::table('rcm_sites', function (Blueprint $table) {
            $table->dropColumn([
                'coordinates_lat',
                'coordinates_lon',
                'location',
                'country_codes',
                'scale',
                'image_gallery'
            ]);
        });


    }
}
