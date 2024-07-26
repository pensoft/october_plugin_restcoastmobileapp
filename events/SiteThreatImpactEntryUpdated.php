<?php

namespace Pensoft\RestcoastMobileApp\Events;

use Illuminate\Queue\SerializesModels;
use Pensoft\RestcoastMobileApp\Models\SiteThreatImpactEntry;

class SiteThreatImpactEntryUpdated
{
    use SerializesModels;

    public $siteThreatImpactEntry;
    public $deleted = false;

    public function __construct(
        SiteThreatImpactEntry $siteThreatImpactEntry,
        bool $deleted = false
    ) {
        $this->siteThreatImpactEntry = $siteThreatImpactEntry;
        $this->deleted = $deleted;
    }
}
