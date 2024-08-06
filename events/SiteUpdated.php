<?php

namespace Pensoft\RestcoastMobileApp\Events;

use Illuminate\Queue\SerializesModels;

class SiteUpdated
{
    use SerializesModels;

    public $siteId;
    public $deleted = false;

    public function __construct(array $data, bool $deleted = false)
    {
        $this->deleted = $deleted;
        $this->siteId = $data['site_id'];
    }
}
