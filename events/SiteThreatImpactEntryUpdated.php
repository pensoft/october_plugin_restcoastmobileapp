<?php

namespace Pensoft\RestcoastMobileApp\Events;

use Illuminate\Queue\SerializesModels;
use Pensoft\RestcoastMobileApp\Models\SiteThreatImpactEntry;

class SiteThreatImpactEntryUpdated
{
    use SerializesModels;

    public $siteThreatImpactEntry;

    public function __construct(SiteThreatImpactEntry $siteThreatImpactEntry)
    {
        $this->siteThreatImpactEntry = $siteThreatImpactEntry;
    }
}
