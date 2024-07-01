<?php namespace Pensoft\Restcoast\Models;

use Model;

class ThreatDefinition extends Model
{
    use \October\Rain\Database\Traits\Validation;

    public $table = 'restcoast_threat_definitions';

    public $rules = [
        'name' => 'required',
        'code' => 'required|unique:restcoast_threat_definitions,code|max:16',
        'short_description' => 'required',
    ];

    public $translatable = [
        'name',
        'short_description',
        'base_outcome',
    ];

    public $jsonable = ['base_outcome'];

    public $implement = ['@RainLab.Translate.Behaviors.TranslatableModel'];

    protected $fillable = [
        'name',
        'code',
        'short_description',
        'base_outcome',
    ];

    public $hasMany = [
        'threat_impact_entries' => [
           SiteThreatImpactEntry::class,
            'key' => 'threat_definition_id'
        ]
    ];

}
