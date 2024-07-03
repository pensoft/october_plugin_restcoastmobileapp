<?php

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class AddImagesFieldsToSitesAndThreats extends Migration
{
    public function up()
    {
        Schema::table(
            'restcoast_sites',
            function (Blueprint $table) {
                $table->string('image')
                    ->after('country')
                    ->nullable();
            });
        Schema::table(
            'restcoast_threat_definitions',
            function (Blueprint $table) {
                $table->string('image')
                    ->after('code')
                    ->nullable();
            });
    }

    public function down()
    {
        Schema::table(
            'restcoast_sites',
            function (Blueprint $table) {
                $table->dropColumn('image');
            }
        );
        Schema::table(
            'restcoast_sites',
            function (Blueprint $table) {
                $table->dropColumn('image');
            }
        );
    }
}
