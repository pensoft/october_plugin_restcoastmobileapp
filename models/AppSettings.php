<?php namespace Pensoft\RestcoastMobileApp\Models;

use Model;
use October\Rain\Database\Traits\Validation;
use Pensoft\RestcoastMobileApp\Events\AppSettingsUpdated;

/**
 * HomeSettings Model
 */
class AppSettings extends Model
{
    use Validation;

    public $implement = [
        '@RainLab.Translate.Behaviors.TranslatableModel',
    ];
    public $settingsCode = 'pensoft_restcoast_settings';
    public $settingsFields = 'fields.yaml';

    /**
     * @var string table associated with the model
     */
    public $table = 'rcm_settings';

    /**
     * @var array rules for validation
     */
    public $rules = [
        'home_map_style' => 'media_file_extension:json',
        'home_map_kml_layer' => 'media_file_extension:kml'
    ];

    /**
     * @var array dates attributes that should be mutated to dates
     */
    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public $translatable = ['privacy_policy', 'about', 'eu_disclaimer'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = 'rcm_settings';
    }

    protected static function boot()
    {
        parent::boot();
        static::saved(function ($model) {
            AppSettingsUpdated::dispatch();
        });
    }

}
