<?php

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class AddCountryToSitesTable extends Migration
{
    public function up()
    {
        Schema::table(
            'restcoast_sites',
            function (Blueprint $table) {
                $table->string('country', 64)
                    ->after('name');
            });
    }

    public function down()
    {
        Schema::table(
            'restcoast_sites',
            function (Blueprint $table) {
                $table->dropColumn('country');
            }
        );
    }
}
