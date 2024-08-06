<?php

namespace Pensoft\RestcoastMobileApp\Events;

use Illuminate\Queue\SerializesModels;
use Pensoft\RestcoastMobileApp\Models\ThreatDefinition;

class ThreatDefinitionUpdated
{
    use SerializesModels;

    public $threatDefinitionId;
    public $deleted = false;

    public function __construct(
        array $data,
        bool $deleted = false
    ) {
        $this->threatDefinitionId = $data['threat_definition_id'];
        $this->deleted = $deleted;
    }
}
