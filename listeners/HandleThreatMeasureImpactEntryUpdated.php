<?php

namespace Pensoft\RestcoastMobileApp\Listeners;

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
                $event->threatMeasureImpactEntryId,
                $event->siteThreatImpactEntryId,
                $event->siteId
            );
        }
        $this->syncService->syncThreatImpactEntries(
            $event->siteThreatImpactEntryId
        );
        $this->syncService->syncMeasureImpactEntries();
    }
}
