<?php

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class AddBaseOutcomeToSiteThreatImpactEntries extends Migration
{
    public function up()
    {
        Schema::table(
            'restcoast_site_threat_impact_entries',
            function (Blueprint $table) {
                $table->mediumText('base_outcome')
                    ->after('outcomes');
            });
    }

    public function down()
    {
        Schema::table(
            'restcoast_site_threat_impact_entries',
            function (Blueprint $table) {
                $table->dropColumn('base_outcome');
            }
        );
    }
}
