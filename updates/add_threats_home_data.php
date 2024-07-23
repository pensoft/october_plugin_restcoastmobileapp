<?php

namespace Pensoft\RestcoastMobileApp\Updates;

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

class AddThreatsHomeData extends Migration
{
    public function up()
    {
        Schema::table('rcm_threat_definitions', function (Blueprint $table) {
            $table->string('thumbnail')->nullable();
            $table->string('definition')->nullable();
        });
    }

    public function down()
    {
        Schema::table('rcm_threat_definitions', function (Blueprint $table) {
            $table->dropColumn([
                'thumbnail',
                'definition',
                'code'
            ]);
        });


    }
}
