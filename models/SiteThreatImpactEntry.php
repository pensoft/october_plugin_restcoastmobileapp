<?php

namespace Pensoft\RestcoastMobileApp\Models;

use Model;
use October\Rain\Database\Traits\Validation;
use Pensoft\RestcoastMobileApp\Events\SiteThreatImpactEntryUpdated;
use Pensoft\RestcoastMobileApp\Services\ValidateDataService;

class SiteThreatImpactEntry extends Model
{

    use Validation;

    // Enable timestamps if needed
    public $timestamps = true;

    // The database table used by the model
    public $table = 'rcm_site_threat_impact_entries';

    private $validateDataService;

    public $rules = [
        'name' => 'required',
        'outcomes.*.scores.*.name' => 'required',
        'outcomes.*.scores.*.score' => 'required|numeric|min:1|max:10',
    ];

    public $jsonable = [
        'content_blocks',
        'outcomes'
    ];

    // Translate the model
    public $implement = [
        '@RainLab.Translate.Behaviors.TranslatableModel',
    ];

    // Add all translatable fields here
    public $translatable = [
        'name',
        'short_description',
        'content_blocks',
        'outcomes'
    ];

    protected $fillable = [
        'name',
        'short_description',
        'content_blocks',
        'outcomes'
    ];

    // Define the relationship
    public $belongsTo = [
        'site' => [
            Site::class,
            'key' => 'site_id'
        ],
        'threat_definition' => [
            ThreatDefinition::class,
            'key' => 'threat_definition_id'
        ]
    ];

    public $hasMany = [
        'measure_impact_entries' => [
            ThreatMeasureImpactEntry::class,
            'key' => 'site_threat_impact_id'
        ]
    ];

    /**
     * Returns of list (id => name) of all Measure Impact entries
     * assigned to this Site Threat Impact entry
     *
     * @return mixed
     */
    public function listRelatedMeasureImpactEntries()
    {
        return $this->measure_impact_entries->pluck('name', 'id')
            ->toArray();
    }

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($model) {
            SiteThreatImpactEntryUpdated::dispatch(
                $model->id,
                $model->site_id,
                false
            );
        });

        static::deleted(function ($model) {
            SiteThreatImpactEntryUpdated::dispatch(
                $model->id,
                $model->site_id,
                true
            );
        });
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->validateDataService = new ValidateDataService();
    }

    public function beforeValidate()
    {
        $this->validateDataService->validateContentBlocks(
            $this->content_blocks
        );
    }

}
