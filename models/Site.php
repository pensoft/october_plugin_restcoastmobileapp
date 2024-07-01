<?php

namespace Pensoft\Restcoast\Models;

use Model;
use Pensoft\Restcoast\Controllers\Sites;
use Pensoft\Restcoast\Services\JsonUploader;

class Site extends Model
{
    use \October\Rain\Database\Traits\Validation;

    public $table = 'restcoast_sites';

    public $rules = [
        'name' => 'required',
        'lat' => 'required',
        'long' => 'required'
    ];

    protected $fillable = [
        'name',
        'short_description',
        'long',
        'lat',
        'content_blocks',
    ];

    public $jsonable = ['content_blocks'];

    public $implement = [
        '@RainLab.Translate.Behaviors.TranslatableModel',
    ];

    public $translatable = [
        'name',
        'short_description',
        'content_blocks',
    ];

    public $hasMany = [
        'threat_impact_entries' => [
            SiteThreatImpactEntry::class,
            'key' => 'site_id'
        ]
    ];
}
