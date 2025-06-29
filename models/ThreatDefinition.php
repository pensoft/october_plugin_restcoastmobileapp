<?php namespace Pensoft\RestcoastMobileApp\Models;

use Model;
use October\Rain\Database\Traits\Validation;
use Pensoft\RestcoastMobileApp\Events\ThreatDefinitionUpdated;
use Pensoft\RestcoastMobileApp\Services\ValidateDataService;
use Pensoft\RestcoastMobileApp\Traits\SyncMedia;

class ThreatDefinition extends Model
{
    use Validation, JsonableFieldsHandler, SyncMedia;

    public $table = 'rcm_threat_definitions';

    private $validateDataService;

    public $rules = [
        'name' => 'required',
        'code' => 'required|unique:rcm_threat_definitions,code|max:16',
        'image' => 'required|media_file_extension:image',
        'outcome_image' => 'media_file_extension:image',
        'short_description' => 'required',
    ];

    public $jsonable = ['base_outcome', 'content_blocks'];

    // Translate the model
    public $implement = [
        '@RainLab.Translate.Behaviors.TranslatableModel',
    ];

    // Add all translatable fields here
    public $translatable = [
        'name',
        'short_description',
        'base_outcome',
        'content_blocks',
        'outcome_name'
    ];

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

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->validateDataService = new ValidateDataService();
    }

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($model) {
            ThreatDefinitionUpdated::dispatch($model->id, false);
        });

        static::deleted(function ($model) {
            ThreatDefinitionUpdated::dispatch($model->id, true);
        });

        static::deleting(function ($model) {
            if (count($model->threat_impact_entries)) {
                $threatImpactEntriesNames = $model->threat_impact_entries
                    ->pluck('name')->toArray();
                $message = sprintf(
                    "%s cannot be deleted because it has the following
                    Site Threat Impact Entries assigned to it: %s",
                    $model->name,
                    implode(', ', $threatImpactEntriesNames)
                );
                throw new \ValidationException([
                    'threat_definition' => $message
                ]);
            }
        });
    }

    /**
     * @return string[]
     */
    public function getMediaPathFields(): array
    {
        return ['image', 'outcome_image'];
    }

    /**
     * @return string[]
     */
    public function getGroupedContentBlockRepeaters(): array
    {
        return ['content_blocks', 'base_outcome'];
    }

}
