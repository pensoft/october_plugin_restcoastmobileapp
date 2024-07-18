<?php namespace Pensoft\Restcoast\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * CreateHomeSettingsTable Migration
 */
class CreateSettingsTable extends Migration {
    public function up() {
        Schema::create( 'rcm_settings', function ( Blueprint $table ) {
            Schema::create( 'rcm_settings', function ( $table ) {
                $table->increments( 'id' );
                $table->string( 'item' )->index();
                $table->text( 'value' )->nullable();
                $table->timestamps();
            } );
        } );
    }

    public function down() {
        Schema::dropIfExists( 'rcm_settings' );
    }
}
