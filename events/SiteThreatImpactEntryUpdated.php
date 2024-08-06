<?php

namespace Pensoft\RestcoastMobileApp\Events;

use Illuminate\Queue\SerializesModels;
use Pensoft\RestcoastMobileApp\Models\SiteThreatImpactEntry;

class SiteThreatImpactEntryUpdated
{
    use SerializesModels;

    public $siteThreatImpactEntryId;
    public $siteId;
    public $deleted = false;

    public function __construct(
        array $data,
        bool $deleted = false
    ) {
        $this->siteThreatImpactEntryId = $data['site_threat_impact_entry_id'];
        $this->siteId = $data['site_id'];
        $this->deleted = $deleted;
    }
}
