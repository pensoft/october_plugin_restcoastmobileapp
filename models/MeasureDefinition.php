<?php

namespace Pensoft\RestcoastMobileApp\Models;

use Model;
use October\Rain\Database\Traits\Validation;
use Pensoft\RestcoastMobileApp\Events\MeasureDefinitionUpdated;

class MeasureDefinition extends Model
{
    use Validation;

    public $table = 'rcm_measure_definitions';

    public $rules = [
        'name' => 'required',
        'code' => 'required|unique:rcm_measure_definitions,code|max:16',
        'short_description' => 'required',
    ];

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
            'key' => 'measure_definition_id'
        ]
    ];

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($model) {
            MeasureDefinitionUpdated::dispatch();
        });

        static::deleted(function ($model) {
            MeasureDefinitionUpdated::dispatch();
        });

        static::deleting(function ($model) {
            if (count($model->measure_impact_entries)) {
                $measureImpactEntriesNames = $model->measure_impact_entries
                    ->pluck('name')->toArray();
                $message = sprintf(
                    "%s cannot be deleted because it has the following
                    Measure Threat Impact Entries assigned to it: %s",
                    $model->name,
                    implode(', ', $measureImpactEntriesNames)
                );
                throw new \ValidationException([
                    'measure_definition' => $message
                ]);
            }
        });
    }

}
