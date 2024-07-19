<?php
namespace Pensoft\RestcoastMobileApp\Models;

use Event;
use Model;
use October\Rain\Database\Traits\Validation;
use Pensoft\RestcoastMobileApp\Extensions\JsonableModel;
use Pensoft\RestcoastMobileApp\Events\SiteUpdated;
use Pensoft\RestcoastMobileApp\Services\ValidateDataService;
use ValidationException;

class Site extends Model
{
    use Validation;

    public $table = 'rcm_sites';
    private $validateDataService;

    public $rules = [
        'name' => 'required',
        'lat' => 'required',
        'long' => 'required',
        'country' => 'required',
        'content_blocks.youtube.videoId' => 'required|size:10'
    ];

    public $jsonable = ['content_blocks'];

    public $implement = [
        '@RainLab.Translate.Behaviors.TranslatableModel',
        JsonableModel::class
    ];

    protected $fillable = [
        'name',
        'short_description',
        'long',
        'lat',
        'content_blocks',
    ];

    // Add all translatable fields here
    public $translatable = [
        'name',
        'country',
        'short_description',
        'content_blocks',
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
            Event::fire(new SiteUpdated($model));
        });
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->validateDataService = new ValidateDataService();
    }

    /**
     * @throws ValidationException
     */
    public function beforeValidate()
    {
        // Initialize rules for the content_blocks repeater field
        $rules = [];
        $contentBlocks = $this->content_blocks;

        if (is_array($contentBlocks)) {
            foreach ($contentBlocks as $index => $block) {
                // Only validate if the block type is 'youtube'
                if (isset($block['_group']) && $block['_group'] == 'youtube') {
                    $rules["content_blocks.$index.videoId"] = 'required|string|size:10';
                }
            }
        }

        $this->rules = $rules;
        $this->validateDataService->validateContentBlocks($this->content_blocks);
    }

}
