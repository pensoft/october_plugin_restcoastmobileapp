<?php
namespace Pensoft\RestcoastMobileApp\Models;

use Model;

class ThreatMeasureImpactEntry extends Model
{
    // Enable timestamps if needed
    public $timestamps = true;

    // The database table used by the model
    public $table = 'rcm_threat_measure_impact_entries';

    public $rules = [
        'name' => 'required',
    ];

    public $jsonable = ['content_blocks'];

    // Translate the model
    public $implement = [
        '@RainLab.Translate.Behaviors.TranslatableModel',
    ];

    public $translatable = [
        'name',
        'short_description',
        'content_blocks',
    ];

    protected $fillable = [
        'name',
        'short_description',
        'content_blocks',
    ];

    // Define the relationship
    public $belongsTo = [
        'measure_definition' => [
            MeasureDefinition::class,
            'key' => 'measure_definition_id',
        ],
        'site_threat_impact' => [
            'Pensoft\RestcoastMobileApp\Models\SiteThreatImpactEntry',
            'key' => 'site_threat_impact_id',
        ]
    ];

    public function listRelatedMeasureImpactEntries() {
        return ThreatMeasureImpactEntry::query()
            ->where(
                'measure_definition_id',
                '=',
                $this->measure_definition->id
            )
            ->get()
            ->pluck('name', 'id')
            ->toArray();
    }
}
