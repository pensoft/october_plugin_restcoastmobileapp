<?php

namespace Pensoft\RestcoastMobileApp\Listeners;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Pensoft\RestcoastMobileApp\Events\SiteUpdated;
use Pensoft\RestcoastMobileApp\Models\Site;
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
     * @param int $siteId
     * @return void
     * @throws FileNotFoundException
     */
    public function updateMediaWithBucket(int $siteId) {
        $site = Site::find($siteId);
        $mediaPaths = $site->getAllMediaPaths();
        foreach ($mediaPaths as $imagePath) {
            $this->syncService->syncMediaFile($imagePath);
        }
    }

    /**
     * @param SiteUpdated $event
     * @return void
     * @throws FileNotFoundException
     */
    public function handle(SiteUpdated $event)
    {
        try {
            $this->updateMediaWithBucket($event->siteId);
        } catch (FileNotFoundException $e) {}

        if ($event->deleted) {
            $this->syncService->deleteSite($event->siteId);
        } else {
            $this->syncService->syncSites($event->siteId);
        }
        $this->syncService->syncThreatDefinitions();
        $this->syncService->syncAppSettings();
    }

}
