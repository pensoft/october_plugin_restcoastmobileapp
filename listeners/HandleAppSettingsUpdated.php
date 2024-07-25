<?php

namespace Pensoft\RestcoastMobileApp\listeners;

use Pensoft\RestcoastMobileApp\Events\AppSettingsUpdated;
use Pensoft\RestcoastMobileApp\Services\SyncDataService;

class HandleAppSettingsUpdated
{
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
