<?php

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateSiteThreatImpactEntriesTable extends Migration
{
    public function up()
    {
        Schema::create(
            'restcoast_site_threat_impact_entries',
            function (Blueprint $table) {
                $table->increments('id');
                $table->string('name', 64);
                $table->integer('site_id')
                    ->unsigned()
                    ->nullable();
                $table->integer('threat_definition_id')
                    ->unsigned()
                    ->nullable();
                $table->foreign('site_id', 'site_fk')
                    ->references('id')
                    ->on('restcoast_sites')
                    ->onDelete('cascade');

                $table->foreign('threat_definition_id', 'threat_def_fk')
                    ->references('id')
                    ->on('restcoast_threat_definitions')
                    ->onDelete('cascade');
                $table->mediumText('short_description')->nullable();
                $table->json('content_blocks')->nullable();
                $table->timestamps();
            });
    }

    public function down()
    {
        Schema::table('restcoast_site_threat_impact_entries', function (Blueprint $table) {
            // Drop foreign key constraints with the shorter names
            $table->dropForeign('site_fk');
            $table->dropForeign('threat_def_fk');
        });
        Schema::dropIfExists('restcoast_site_threat_impact_entries');
    }
}
