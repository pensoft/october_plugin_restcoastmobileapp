<?php

namespace Pensoft\RestcoastMobileApp\Events;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ThreatMeasureImpactEntryUpdated
{
    use Dispatchable, Queueable, InteractsWithQueue, SerializesModels;

    public $threatMeasureImpactEntryId;
    public $siteThreatImpactEntryId;
    public $siteId;
    public $deleted = false;

    public function __construct(
        $threatMeasureImpactEntryId,
        $siteThreatImpactEntryId,
        $siteId,
        bool $deleted = false
    ) {
        $this->threatMeasureImpactEntryId = $threatMeasureImpactEntryId;
        $this->siteThreatImpactEntryId = $siteThreatImpactEntryId;
        $this->siteId = $siteId;
        $this->deleted = $deleted;
    }
}
