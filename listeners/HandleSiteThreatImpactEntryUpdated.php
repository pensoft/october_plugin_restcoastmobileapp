<?php

namespace Pensoft\RestcoastMobileApp\Listeners;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Pensoft\RestcoastMobileApp\Events\SiteThreatImpactEntryUpdated;
use Pensoft\RestcoastMobileApp\Services\SyncDataService;

class HandleSiteThreatImpactEntryUpdated implements ShouldQueue
{
    use InteractsWithQueue, Queueable, UseSyncQueue;

    protected $syncService;

    public function __construct(SyncDataService $syncService)
    {
        $this->syncService = $syncService;
    }

    public function handle(SiteThreatImpactEntryUpdated $event)
    {
        if ($event->deleted) {
            $this->syncService->deleteSiteThreatImpactEntry(
                $event->siteThreatImpactEntryId,
                $event->siteId
            );
        }
        // Updates Sites data, because the Site Impact Entry's Site
        // may have been changed.
        $this->syncService->syncSites();
        $this->syncService->syncThreatDefinitions();
        $this->syncService->syncThreatImpactEntries(
            $event->siteThreatImpactEntryId
        );
    }
}
