<?php

namespace Pensoft\RestcoastMobileApp\Events;

use Illuminate\Queue\SerializesModels;
use Pensoft\RestcoastMobileApp\Models\Site;

class SiteUpdated
{
    use SerializesModels;

    public $site;

    public function __construct(Site $site)
    {
        $this->site = $site;
    }
}
