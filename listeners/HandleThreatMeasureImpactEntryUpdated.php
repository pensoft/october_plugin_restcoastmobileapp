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
        if ($event->deleted) {
            $this->syncService->deleteThreatMeasureImpactEntry(
                $event->threatMeasureImpactEntry
            );
        }
        $this->syncService->syncThreatImpactEntries(
            $event->threatMeasureImpactEntry->site_threat_impact->id
        );
        $this->syncService->syncMeasureImpactEntries();
    }
}
