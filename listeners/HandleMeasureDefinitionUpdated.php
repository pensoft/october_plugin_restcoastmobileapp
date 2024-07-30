<?php

namespace Pensoft\RestcoastMobileApp\listeners;

use Pensoft\RestcoastMobileApp\Events\MeasureDefinitionUpdated;
use Pensoft\RestcoastMobileApp\Services\SyncDataService;

class HandleMeasureDefinitionUpdated
{
    protected $syncService;

    public function __construct(SyncDataService $syncService)
    {
        $this->syncService = $syncService;
    }

    public function handle(MeasureDefinitionUpdated $event)
    {
        $this->syncService->syncThreatImpactEntries();
        $this->syncService->syncMeasureImpactEntries();
    }
}
