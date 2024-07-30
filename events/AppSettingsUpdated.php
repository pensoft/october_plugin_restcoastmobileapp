<?php

namespace Pensoft\RestcoastMobileApp\Events;

use Illuminate\Queue\SerializesModels;
use Pensoft\RestcoastMobileApp\Models\AppSettings;

class AppSettingsUpdated
{
    use SerializesModels;

    public $settings;

    public function __construct(AppSettings $settings)
    {
        $this->settings = $settings;
    }
}
