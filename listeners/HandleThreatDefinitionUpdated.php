<?php

namespace Pensoft\RestcoastMobileApp\Listeners;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Pensoft\RestcoastMobileApp\Events\ThreatDefinitionUpdated;
use Pensoft\RestcoastMobileApp\Services\SyncDataService;

class HandleThreatDefinitionUpdated implements ShouldQueue
{
    use InteractsWithQueue, Queueable, UseSyncQueue;

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
        $this->syncService->syncThreatDefinitions(
            $event->threatDefinitionId
        );
        $this->syncService->syncAppSettings();
    }
}
