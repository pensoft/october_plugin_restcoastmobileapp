<?php

namespace Pensoft\RestcoastMobileApp\Events;

use Illuminate\Queue\SerializesModels;
use Pensoft\RestcoastMobileApp\Models\ThreatDefinition;

class ThreatDefinitionUpdated
{
    use SerializesModels;

    public $threatDefinition;
    public $deleted = false;

    public function __construct(
        ThreatDefinition $threatDefinition,
        bool $deleted = false
    ) {
        $this->threatDefinition = $threatDefinition;
        $this->deleted = $deleted;
    }
}
