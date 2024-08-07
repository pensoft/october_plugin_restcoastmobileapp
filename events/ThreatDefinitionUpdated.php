<?php

namespace Pensoft\RestcoastMobileApp\Events;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ThreatDefinitionUpdated
{
    use Dispatchable, Queueable, InteractsWithQueue, SerializesModels;

    public $threatDefinitionId;
    public $deleted = false;

    public function __construct(
        int $threatDefinitionId,
        bool $deleted = false
    ) {
        $this->threatDefinitionId = $threatDefinitionId;
        $this->deleted = $deleted;
    }
}
