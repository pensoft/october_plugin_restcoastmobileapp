<?php

namespace Pensoft\RestcoastMobileApp\Updates;

use October\Rain\Database\Updates\Migration;
use Schema;

class UpdateThreatDefinitionsTable extends Migration
{
    public function up()
    {
        Schema::table('rcm_threat_definitions', function ($table) {
            $table->json('content_blocks')
                ->after('short_description')
                ->nullable();
            $table->string('outcome_name', 64)->nullable();
            $table->string('outcome_image')->nullable();
        });
    }

    public function down()
    {
        Schema::table('rcm_threat_definitions', function ($table) {
            $table->dropColumn([
                'content_blocks',
                'outcome_name',
                'outcome_image'
            ]);
        });
    }
}
