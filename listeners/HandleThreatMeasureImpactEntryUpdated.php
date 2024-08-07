<?php

namespace Pensoft\RestcoastMobileApp\Listeners;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Pensoft\RestcoastMobileApp\Events\ThreatMeasureImpactEntryUpdated;
use Pensoft\RestcoastMobileApp\Services\SyncDataService;

class HandleThreatMeasureImpactEntryUpdated implements ShouldQueue
{
    use InteractsWithQueue, Queueable, UseSyncQueue;

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
