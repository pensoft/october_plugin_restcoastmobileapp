<?php

namespace Pensoft\RestcoastMobileApp\Events;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SiteThreatImpactEntryUpdated
{
    use Dispatchable, Queueable, InteractsWithQueue, SerializesModels;

    public $siteThreatImpactEntryId;
    public $siteId;
    public $deleted = false;

    public function __construct(
        $siteThreatImpactEntryId,
        $siteId,
        bool $deleted = false
    ) {
        $this->siteThreatImpactEntryId = $siteThreatImpactEntryId;
        $this->siteId = $siteId;
        $this->deleted = $deleted;
    }
}
