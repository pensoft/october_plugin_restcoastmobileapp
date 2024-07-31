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
        if (!$event->deleted) {
            $this->syncService->syncSites();
            $this->syncService->syncThreatImpactEntries();
        }
        $this->syncService->syncThreatDefinitions();
        $this->syncService->syncAppSettings();
    }
}
