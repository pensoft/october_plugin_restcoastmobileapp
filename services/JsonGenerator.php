<?php

namespace Pensoft\RestcoastMobileApp\Services;
use October\Rain\Database\Model;
use Pensoft\RestcoastMobileApp\Extensions\JsonableModel;

class JsonGenerator {
    public function generateJson( Model $entry ) {
        // Your logic to generate the JSON data

        if ( $entry->isClassExtendedWith( JsonableModel::class ) ) {
            $data = $entry->generateJson();
        } else {
            $data = $entry->attributesToArray();
        }

        return json_encode( $data, JSON_PRETTY_PRINT );
    }
}
