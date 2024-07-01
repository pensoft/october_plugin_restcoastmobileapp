<?php

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class UpdateMeasureImpactEntriesTable extends Migration
{
    public function up()
    {
        Schema::table(
            'restcoast_threat_measure_impact_entries',
            function (Blueprint $table) {
                $table->integer('site_threat_impact_id')
                    ->unsigned()
                    ->nullable();
                $table->foreign(
                    'site_threat_impact_id',
                    'site_threat_impact_fk'
                )
                    ->references('id')
                    ->on('restcoast_site_threat_impact_entries')
                    ->onDelete('cascade');
            });
    }

    public function down()
    {
        Schema::table('restcoast_threat_measure_impact_entries', function (Blueprint $table) {
            $table->dropForeign('site_threat_impact_fk');
        });
        Schema::table(
            'restcoast_threat_measure_impact_entries',
            function (Blueprint $table) {
                $table->dropColumn('site_threat_impact_id');
            }
        );
    }
}
