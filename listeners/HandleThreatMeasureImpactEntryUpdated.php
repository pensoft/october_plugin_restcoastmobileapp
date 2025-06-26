<?php

namespace Pensoft\RestcoastMobileApp\Listeners;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Pensoft\RestcoastMobileApp\Events\ThreatMeasureImpactEntryUpdated;
use Pensoft\RestcoastMobileApp\Models\ThreatMeasureImpactEntry;
use Pensoft\RestcoastMobileApp\Services\SyncDataService;

class HandleThreatMeasureImpactEntryUpdated implements ShouldQueue
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
    public function updateMediaWithBucket(int $threatMeasureImpactEntryId)
    {
        $threatDefinition = ThreatMeasureImpactEntry::find($threatMeasureImpactEntryId);
        $mediaPaths = $threatDefinition->getAllMediaPaths();
        foreach ($mediaPaths as $imagePath) {
            $this->syncService->syncMediaFile($imagePath);
        }
    }

    /**
     * @param ThreatMeasureImpactEntryUpdated $event
     * @return void
     * @throws FileNotFoundException
     */
    public function handle(ThreatMeasureImpactEntryUpdated $event)
    {
        try {
            $this->updateMediaWithBucket($event->threatMeasureImpactEntryId);
        } catch (FileNotFoundException $e) {
        }

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
