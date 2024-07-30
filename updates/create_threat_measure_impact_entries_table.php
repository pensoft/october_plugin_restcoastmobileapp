<?php

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateThreatMeasureImpactEntriesTable extends Migration
{
    public function up()
    {
        Schema::create(
            'rcm_threat_measure_impact_entries',
            function (Blueprint $table) {
                $table->increments('id');
                $table->string('name', 64);
                $table->integer('measure_definition_id')
                    ->unsigned()
                    ->nullable();
                $table->foreign(
                    'measure_definition_id',
                    'measure_fk'
                )
                    ->references('id')
                    ->on('rcm_measure_definitions')
                    ->onDelete('cascade');
                $table->integer('site_threat_impact_id')
                    ->unsigned()
                    ->nullable();
                $table->foreign(
                    'site_threat_impact_id',
                    'site_threat_impact_fk'
                )
                    ->references('id')
                    ->on('rcm_site_threat_impact_entries')
                    ->onDelete('cascade');

                $table->mediumText('short_description')->nullable();
                $table->json('content_blocks')->nullable();
                $table->json('outcomes')->nullable();
                $table->timestamps();
            });
    }

    public function down()
    {
        Schema::table('rcm_threat_measure_impact_entries', function (Blueprint $table) {
            $table->dropForeign('measure_fk');
            $table->dropForeign('site_threat_impact_fk');
        });
        Schema::dropIfExists('rcm_threat_measure_impact_entries');
    }
}
