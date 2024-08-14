<?php
namespace Pensoft\RestcoastMobileApp\Models;

use System\Behaviors\SettingsModel;
use Cache;

/**
 * This class is required just to overwrite "afterModelSave" function of "SettingsModel".
 * It's required because that function calls "php artisan queue:restart".
 */
class CustomAppSettings extends SettingsModel
{

    public function afterModelSave()
    {
        Cache::forget($this->getCacheKey());

        return;
    }

}
