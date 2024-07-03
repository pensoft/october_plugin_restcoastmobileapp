<?php

namespace Pensoft\Restcoast\Models;

use Model;
use Pensoft\Restcoast\Services\JsonUploader;

class Site extends Model
{
    use \October\Rain\Database\Traits\Validation;

    public $table = 'restcoast_sites';

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
            $uploader = new JsonUploader();
            $sites = Site::query()->get()->toArray();
            $uploader->uploadJson(
                $sites,
                'l/en/sites.json'
            );
        });

    }
}
