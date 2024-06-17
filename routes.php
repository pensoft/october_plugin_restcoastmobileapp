<?php
use Pensoft\Restcoast\Models\Event;
Route::get('/api/pensoft/restcoast/fetch', function(){
    $events = Event::all();
    echo '<pre>';
//    print_r($events->toArray());
//
//    return response()->json($events);

    $translatedEvents = $events->map(function($event) {
        $event->translateContext('fr');
        return $event;
    });
    print_r($translatedEvents->toArray());
    return response()->json($translatedEvents);
});
