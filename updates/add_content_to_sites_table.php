<?php

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class AddContentBlocksToSitesTable extends Migration
{
    public function up()
    {
        Schema::table(
            'restcoast_sites',
            function (Blueprint $table) {
                $table->json('content_blocks')
                    ->after('short_description')
                    ->nullable();
            });
    }

    public function down()
    {
        Schema::table(
            'restcoast_sites',
            function (Blueprint $table) {
                $table->dropColumn('content_blocks');
            }
        );
    }
}
