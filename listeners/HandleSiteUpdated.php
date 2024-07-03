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
        $this->syncService->syncSites();
    }
}
