<?php

namespace Pensoft\RestcoastMobileApp\Models;

use Model;
use October\Rain\Database\Traits\Validation;

class MeasureDefinition extends Model
{
    use Validation;

    public $table = 'rcm_measure_definitions';

    public $rules = [
        'name' => 'required',
        'code' => 'required|unique:rcm_measure_definitions,code|max:16',
        'short_description' => 'required',
    ];

    public $jsonable = ['content_blocks'];

    // Translate the model
    public $implement = [
        '@RainLab.Translate.Behaviors.TranslatableModel',
    ];

    public $translatable = [
        'name',
        'short_description',
    ];

    protected $fillable = [
        'name',
        'code',
        'short_description',
    ];

    public $hasMany = [
        'measure_impact_entries' => [
            ThreatMeasureImpactEntry::class,
            'key' => 'measure_definition'
        ]
    ];

}
