<?php

namespace Pensoft\RestcoastMobileApp\Events;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MeasureDefinitionUpdated
{
    use Dispatchable, Queueable, InteractsWithQueue, SerializesModels;

    public function __construct() {}
}
