<?php
namespace YourVendor\YourPlugin\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateSiteThreatTable extends Migration
{
    public function up()
    {
        Schema::create('pensoft_restcoast_site_threat', function ($table) {
            $table->integer('site_id')->unsigned();
            $table->integer('threat_id')->unsigned();
            $table->primary(['site_id', 'threat_id']);

            $table->foreign('site_id')
                ->references('id')
                ->on('pensoft_restcoast_sites')
                ->onDelete('cascade');

            $table->foreign('threat_id')
                ->references('id')
                ->on('pensoft_restcoast_threats')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pensoft_restcoast_site_threat');
    }
}
