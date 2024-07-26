<?php

namespace Pensoft\RestcoastMobileApp\Events;

use Illuminate\Queue\SerializesModels;
use Pensoft\RestcoastMobileApp\Models\Site;

class SiteUpdated
{
    use SerializesModels;

    public $site;
    public $deleted = false;

    public function __construct(Site $site, bool $deleted = false)
    {
        $this->deleted = $deleted;
        $this->site = $site;
    }
}
