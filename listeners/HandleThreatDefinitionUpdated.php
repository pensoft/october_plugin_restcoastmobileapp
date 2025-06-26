<?php

namespace Pensoft\RestcoastMobileApp\Listeners;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Pensoft\RestcoastMobileApp\Events\ThreatDefinitionUpdated;
use Pensoft\RestcoastMobileApp\Models\ThreatDefinition;
use Pensoft\RestcoastMobileApp\Services\SyncDataService;

class HandleThreatDefinitionUpdated implements ShouldQueue
{
    use InteractsWithQueue, Queueable, UseSyncQueue;

    protected $syncService;

    public function __construct(SyncDataService $syncService)
    {
        $this->syncService = $syncService;
    }

    /**
     * @throws FileNotFoundException
     */
    public function updateMediaWithBucket(int $threatDefinitionId)
    {
        $threatDefinition = ThreatDefinition::find($threatDefinitionId);
        $mediaPaths = $threatDefinition->getAllMediaPaths();
        foreach ($mediaPaths as $imagePath) {
            $this->syncService->syncMediaFile($imagePath);
        }
    }

    /**
     * @param ThreatDefinitionUpdated $event
     * @return void
     */
    public function handle(ThreatDefinitionUpdated $event)
    {
        try {
            $this->updateMediaWithBucket($event->threatDefinitionId);
        } catch (FileNotFoundException $exception) {
        }
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
