<?php namespace Pensoft\RestcoastMobileApp\Models;

use Event;
use Model;
use October\Rain\Database\Traits\Validation;
use Pensoft\RestcoastMobileApp\Events\ThreatDefinitionUpdated;
use Pensoft\RestcoastMobileApp\Services\ValidateDataService;

class ThreatDefinition extends Model
{
    use Validation;

    public $table = 'rcm_threat_definitions';

    private $validateDataService;

    public $rules = [
        'name' => 'required',
        'code' => 'required|unique:rcm_threat_definitions,code|max:16',
        'image' => 'media_image',
        'outcome_image' => 'media_image',
        'short_description' => 'required',
    ];

    public $customMessages = [
        'image.media_image' => 'The :attribute must be a valid image (jpeg, bmp, png, gif).',
        'outcome_image.media_image' => 'The :attribute must be a valid image (jpeg, bmp, png, gif).',
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
            Event::fire(new ThreatDefinitionUpdated($model));
        });

        static::deleted(function ($model) {
            Event::fire(new ThreatDefinitionUpdated($model, true));
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

}
