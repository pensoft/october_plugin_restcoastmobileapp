<?php namespace Pensoft\RestcoastMobileApp\Models;

use Event;
use Model;
use October\Rain\Database\Traits\Validation;
use Pensoft\RestcoastMobileApp\Events\AppSettingsUpdated;
use System\Behaviors\SettingsModel;

/**
 * HomeSettings Model
 */
class AppSettings extends Model
{
    use Validation;

    public $implement = [
        SettingsModel::class,
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
    public $rules = [];

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
            Event::fire(new AppSettingsUpdated($model));
        });
    }
}
