<?php

namespace Pensoft\RestcoastMobileApp\Models;

use Model;
use October\Rain\Database\Traits\Validation;
use Pensoft\RestcoastMobileApp\Events\SiteUpdated;
use Pensoft\RestcoastMobileApp\Services\ValidateDataService;

class Site extends Model
{
    use Validation, JsonableFieldsHandler;

    public $table = 'rcm_sites';
    private $validateDataService;

    public $rules = [
        'name' => 'required',
        'lat' => 'required',
        'long' => 'required',
        'country' => 'required',
        'stakeholders.*.name' => 'required',
        'stakeholders.*.image' => 'required|media_image',
        'image_gallery.*.image' => 'required|media_image'
    ];

    public $customMessages = [
        'image_gallery.*.image.media_image' => 'The :attribute must be a valid image (jpeg, bmp, png, gif).',
        'stakeholders.*.image.media_image' => 'The :attribute must be a valid image (jpeg, bmp, png, gif).'
    ];

    public $jsonable = [
        'content_blocks',
        'country_codes',
        'image_gallery',
        'stakeholders'
    ];

    public $implement = [
        '@RainLab.Translate.Behaviors.TranslatableModel',
    ];

    protected $fillable = [
        'name',
        'short_description',
        'long',
        'lat',
        'content_blocks',
        'stakeholders',
        'country',
    ];

    // Add all translatable fields here
    public $translatable = [
        'name',
        'country',
        'scale',
        'short_description',
        'content_blocks',
        'stakeholders'
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
            SiteUpdated::dispatch($model->id, false);
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
                    'site' => $message
                ]);
            }
        });

        static::deleted(function (Site $model) {
            SiteUpdated::dispatch($model->id, true);
        });
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->validateDataService = new ValidateDataService();
    }

    public function beforeValidate()
    {
        $this->validateDataService->validateContentBlocks($this->content_blocks);
    }

}
