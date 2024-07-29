<?php

namespace Pensoft\RestcoastMobileApp\Events;

use Illuminate\Queue\SerializesModels;
use Pensoft\RestcoastMobileApp\Models\ThreatMeasureImpactEntry;

class ThreatMeasureImpactEntryUpdated
{
    use SerializesModels;

    public $threatMeasureImpactEntry;
    public $deleted = false;

    public function __construct(
        ThreatMeasureImpactEntry $threatMeasureImpactEntry,
        bool $deleted = false
    ) {
        $this->threatMeasureImpactEntry = $threatMeasureImpactEntry;
        $this->deleted = $deleted;
    }
}
