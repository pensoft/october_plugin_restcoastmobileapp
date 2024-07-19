<?php namespace Pensoft\RestcoastMobileApp\Models;

use Event;
use Model;
use Pensoft\RestcoastMobileApp\Events\ThreatDefinitionUpdated;

class ThreatDefinition extends Model
{
    use \October\Rain\Database\Traits\Validation;

    public $table = 'rcm_threat_definitions';

    public $rules = [
        'name' => 'required',
        'code' => 'required|unique:rcm_threat_definitions,code|max:16',
        'short_description' => 'required',
    ];

    public $jsonable = ['base_outcome'];

    // Translate the model
    public $implement = [
        '@RainLab.Translate.Behaviors.TranslatableModel',
    ];

    // Add all translatable fields here
    public $translatable = [
        'name',
        'short_description',
        'base_outcome',
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

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($model) {
            Event::fire(new ThreatDefinitionUpdated($model));
        });

//            $relatedThreatImpactEntriesIds = SiteThreatImpactEntry::query()
//                ->select('id', 'site_id')
//                ->distinct('site_id')
//                ->where(
//                    'threat_definition_id',
//                    '=',
//                    $model->id
//                )
//                ->get()
//                ->map(function ($entry) {
//                    return $entry['site_id'];
//                })
//                ->toArray();
//
//            $sites = Site::query()
//                ->select('id', 'name', 'image')
//                ->whereIn('id', $relatedThreatImpactEntriesIds)
//                ->get()
//                ->toArray();
//
//            $threatDefinition = [
//                'threat_name' => $model->name,
//                'threat_image' => $mediaFolder . '/' . $model->image,
//                'threat_description' => $model->short_description,
//                'sites' => $sites
//            ];
//
//            $fileName = "l/en/threat-definition/{$model->id}.json";
//            $uploader->uploadJson(
//                $threatDefinition,
//                $fileName
//            );
//        });
    }

}
