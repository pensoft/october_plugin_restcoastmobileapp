<?php

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateThreatMeasureImpactEntriesTable extends Migration
{
    public function up()
    {
        Schema::create(
            'restcoast_threat_measure_impact_entries',
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
                    ->on('restcoast_measure_definitions')
                    ->onDelete('cascade');
                $table->mediumText('short_description')->nullable();
                $table->json('content_blocks')->nullable();
                $table->json('outcomes')->nullable();
                $table->timestamps();
            });
    }

    public function down()
    {
        Schema::table('restcoast_threat_measure_impact_entries', function (Blueprint $table) {
            $table->dropForeign('measure_fk');
        });
        Schema::dropIfExists('restcoast_threat_measure_impact_entries');
    }
}
