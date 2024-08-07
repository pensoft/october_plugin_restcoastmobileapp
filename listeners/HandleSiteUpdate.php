<?php

namespace Pensoft\RestcoastMobileApp\Listeners;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Pensoft\RestcoastMobileApp\Events\SiteUpdated;
use Pensoft\RestcoastMobileApp\Services\SyncDataService;

class HandleSiteUpdate implements ShouldQueue
{
    use InteractsWithQueue, Queueable, UseSyncQueue;

    protected $syncService;

    public function __construct(SyncDataService $service)
    {
        $this->syncService = $service;
    }

    /**
     * @param SiteUpdated $event
     * @return void
     */
    public function handle(SiteUpdated $event)
    {
        if ($event->deleted) {
            $this->syncService->deleteSite($event->siteId);
        } else {
            $this->syncService->syncSites($event->siteId);
        }
        $this->syncService->syncThreatDefinitions();
        $this->syncService->syncAppSettings();
    }
}
