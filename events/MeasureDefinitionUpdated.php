<?php

namespace Pensoft\RestcoastMobileApp\Events;

use Illuminate\Queue\SerializesModels;
use Pensoft\RestcoastMobileApp\Models\MeasureDefinition;

class MeasureDefinitionUpdated
{
    use SerializesModels;

    public $threatDefinition;
    public $deleted = false;

    /**
     * @param MeasureDefinition $measureDefinition
     * @param bool $deleted
     */
    public function __construct(
        MeasureDefinition $measureDefinition,
        bool $deleted = false
    ) {
        $this->threatDefinition = $measureDefinition;
        $this->deleted = $deleted;
    }
}
