<?php

namespace Pensoft\RestcoastMobileApp\listeners;

use Pensoft\RestcoastMobileApp\Events\SiteUpdated;
use Pensoft\RestcoastMobileApp\Services\SyncDataService;

class HandleSiteUpdated
{
    protected $syncService;

    public function __construct(SyncDataService $syncService)
    {
        $this->syncService = $syncService;
    }

    public function handle(SiteUpdated $event)
    {
        if ($event->deleted) {
            $this->syncService->deleteSite($event->site->id);
        } else {
            $this->syncService->syncSites();
        }
        $this->syncService->syncThreatDefinitions();
        $this->syncService->syncAppSettings();
    }
}
