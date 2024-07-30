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
        // If a Threat Definition is updated,
        // we only need to update the Home screen settings nad Threat Definitions
        if (!$event->deleted) {
            $this->syncService->syncSites();
            $this->syncService->syncThreatImpactEntries();
        }
        $this->syncService->syncThreatsDefinitions();
        $this->syncService->syncAppSettings();
    }
}
