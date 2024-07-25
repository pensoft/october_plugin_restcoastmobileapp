<?php
namespace Pensoft\RestcoastMobileApp\Models;

use Event;
use Model;
use October\Rain\Database\Traits\Validation;
use Pensoft\RestcoastMobileApp\Events\SiteUpdated;

class Site extends Model
{
    use Validation;

    public $table = 'rcm_sites';

    public $rules = [
        'name' => 'required',
        'lat' => 'required',
        'long' => 'required',
        'country' => 'required',
    ];

    public $jsonable = [
        'content_blocks',
        'country_codes',
        'image_gallery',
        'stakeholders'
    ];

    public $implement = [
        '@RainLab.Translate.Behaviors.TranslatableModel',
    ];

    protected $fillable = [
        'name',
        'short_description',
        'long',
        'lat',
        'content_blocks',
        'stakeholders',
        'country',
    ];

    // Add all translatable fields here
    public $translatable = [
        'name',
        'country',
        'short_description',
        'content_blocks',
        'stakeholders'
    ];

    public $hasMany = [
        'threat_impact_entries' => [
            SiteThreatImpactEntry::class,
            'key' => 'site_id'
        ]
    ];

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($model) {
            Event::fire(new SiteUpdated($model));
        });
    }

}
