<?php

namespace Pensoft\RestcoastMobileApp\Models;

use Event;
use Model;
use Pensoft\RestcoastMobileApp\Events\SiteUpdated;

class Site extends Model
{
    use \October\Rain\Database\Traits\Validation;

    public $table = 'rcm_sites';

    public $rules = [
        'name' => 'required',
        'lat' => 'required',
        'long' => 'required',
        'country' => 'required'
    ];

    protected $fillable = [
        'name',
        'short_description',
        'long',
        'lat',
        'content_blocks',
    ];

    public $jsonable = ['content_blocks'];

    // Translate the model
    public $implement = [
        '@RainLab.Translate.Behaviors.TranslatableModel',
    ];

    // Add all translatable fields here
    public $translatable = [
        'name',
        'country',
        'short_description',
        'content_blocks',
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
