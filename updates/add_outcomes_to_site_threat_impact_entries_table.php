<?php

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class AddOutcomesToSiteThreatImpactEntriesTable extends Migration
{
    public function up()
    {
        Schema::table(
            'restcoast_site_threat_impact_entries',
            function (Blueprint $table) {
                $table->json('outcomes')
                    ->after('content_blocks')->nullable();
            });
    }

    public function down()
    {
        Schema::table(
            'restcoast_site_threat_impact_entries',
            function (Blueprint $table) {
                $table->dropColumn('outcomes');
            }
        );
    }
}
