<?php

namespace Pensoft\RestcoastMobileApp\Events;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SiteUpdated
{
    use Dispatchable, Queueable, InteractsWithQueue, SerializesModels;

    public $siteId;
    public $deleted = false;

    public function __construct(int $siteId, bool $deleted = false)
    {
        $this->deleted = $deleted;
        $this->siteId = $siteId;
    }
}
