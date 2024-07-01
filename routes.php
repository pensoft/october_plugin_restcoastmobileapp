<?php

use Pensoft\Restcoast\Controllers\MeasureDefinitions;
use Pensoft\Restcoast\Models\Site;
use Pensoft\Restcoast\Models\ThreatDefinition;
use Pensoft\Restcoast\Services\JsonUploader;

Route::group(['prefix' => 'pensoft/restcoast'], function () {
    Route::any(
        'measure-definitions/:any',
        [MeasureDefinitions::class, 'index']
    )->where('any', '.*');
});


Route::get('/test-upload', function(JsonUploader $uploader){
    $testData = json_encode([
        'test' => 1,
        'testing' => 2
    ]);
//    Storage::disk('gcs')->put('anothertest.json', $testData);
});

Route::get('/test-fetch', function(JsonUploader $uploader){

});
