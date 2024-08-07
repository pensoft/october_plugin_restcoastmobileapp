<?php

namespace Pensoft\RestcoastMobileApp\Events;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AppSettingsUpdated
{
    use Dispatchable, Queueable, InteractsWithQueue, SerializesModels;

    public $settings;

    public function __construct() {}
}
