<?php

namespace Pensoft\RestcoastMobileApp\Listeners;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Pensoft\RestcoastMobileApp\Events\AppSettingsUpdated;
use Pensoft\RestcoastMobileApp\Services\SyncDataService;

class HandleAppSettingsUpdated implements ShouldQueue
{
    use InteractsWithQueue, Queueable, UseSyncQueue;

    protected $syncService;

    public function __construct(SyncDataService $syncService)
    {
        $this->syncService = $syncService;
    }

    public function handle(AppSettingsUpdated $event)
    {
        $this->syncService->syncAppSettings();
    }
}
