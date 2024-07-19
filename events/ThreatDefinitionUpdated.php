<?php

namespace Pensoft\RestcoastMobileApp\Events;

use Illuminate\Queue\SerializesModels;
use Pensoft\RestcoastMobileApp\Models\ThreatDefinition;

class ThreatDefinitionUpdated
{
    use SerializesModels;

    public $threatDefinition;

    public function __construct(ThreatDefinition $threatDefinition)
    {
        $this->threatDefinition = $threatDefinition;
    }
}
