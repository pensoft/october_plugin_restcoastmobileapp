<?php

namespace Pensoft\RestcoastMobileApp\listeners;

use Pensoft\RestcoastMobileApp\Events\ThreatDefinitionUpdated;
use Pensoft\RestcoastMobileApp\Services\SyncDataService;

class HandleThreatDefinitionUpdated
{
    protected $syncService;

    public function __construct(SyncDataService $syncService)
    {
        $this->syncService = $syncService;
    }

    public function handle(ThreatDefinitionUpdated $event)
    {
        $this->syncService->syncThreatsDefinitions();
    }
}
