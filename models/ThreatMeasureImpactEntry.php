<?php

namespace Pensoft\RestcoastMobileApp\Models;

use Model;
use October\Rain\Database\Traits\Validation;
use Pensoft\RestcoastMobileApp\Events\ThreatMeasureImpactEntryUpdated;
use Pensoft\RestcoastMobileApp\Services\ValidateDataService;
use Pensoft\RestcoastMobileApp\Traits\SyncMedia;

class ThreatMeasureImpactEntry extends Model
{

    use Validation, JsonableFieldsHandler, SyncMedia;

    // Enable timestamps if needed
    public $timestamps = true;

    // The database table used by the model
    public $table = 'rcm_threat_measure_impact_entries';

    private $validateDataService;

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
            SiteThreatImpactEntry::class,
            'key' => 'site_threat_impact_id',
        ]
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->validateDataService = new ValidateDataService();
    }

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($model) {
            ThreatMeasureImpactEntryUpdated::dispatch(
                $model->id,
                $model->site_threat_impact->id,
                $model->site_threat_impact->site_id,
                false
            );
        });

        // Before deleting it, check if this entry is used somewhere
        // in the Outcomes of the assigned Site Threat Impact Entry
        static::deleting(function ($model) {
            $outcomes = $model->site_threat_impact->outcomes;
            $usedInOutcomes = false;
            if (!empty($outcomes)) {
                foreach ($outcomes as $outcome) {
                    if (in_array($model->id, $outcome['measures'])) {
                        $usedInOutcomes = true;
                        break;
                    }
                }
            }
            if ($usedInOutcomes) {
                $message = sprintf(
                    "%s cannot be deleted because it is being used in
                    Outcomes field of %s",
                    $model->name,
                    $model->site_threat_impact->name
                );
                throw new \ValidationException([
                    'used_in_outcomes' => $message
                ]);
            }
        });

        static::deleted(function ($model) {
            ThreatMeasureImpactEntryUpdated::dispatch(
                $model->id,
                $model->site_threat_impact->id,
                $model->site_threat_impact->site_id,
                true
            );
        });
    }

    public function beforeValidate()
    {
        $this->validateDataService->validateContentBlocks($this->content_blocks);
    }

    public function getSiteNameAttribute()
    {
        return $this->site_threat_impact ? $this->site_threat_impact->site->name ?? '' : '';
    }

    /**
     * @return string[]
     */
    public function getGroupedContentBlockRepeaters(): array
    {
        return ['content_blocks'];
    }
}
