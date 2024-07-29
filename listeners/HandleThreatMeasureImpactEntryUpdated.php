<?php

namespace Pensoft\RestcoastMobileApp\listeners;

use Pensoft\RestcoastMobileApp\Events\ThreatMeasureImpactEntryUpdated;
use Pensoft\RestcoastMobileApp\Services\SyncDataService;

class HandleThreatMeasureImpactEntryUpdated
{
    protected $syncService;

    public function __construct(SyncDataService $syncService)
    {
        $this->syncService = $syncService;
    }

    public function handle(ThreatMeasureImpactEntryUpdated $event)
    {
        // we only need to update the Home screen settings nad Threat Definitions
        if ($event->deleted) {
            $this->syncService->deleteThreatMeasureImpactEntry(
                $event->threatMeasureImpactEntry
            );
        }
        $this->syncService->syncMeasureImpactEntries();
    }
}
