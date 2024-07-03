<?php namespace Pensoft\Restcoast\Models;

use Model;
use Pensoft\Restcoast\Services\JsonUploader;
use RainLab\Translate\Models\Locale;

class ThreatDefinition extends Model
{
    use \October\Rain\Database\Traits\Validation;

    public $table = 'restcoast_threat_definitions';

    public $rules = [
        'name' => 'required',
        'code' => 'required|unique:restcoast_threat_definitions,code|max:16',
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
            $uploader = new JsonUploader();
            $mediaFolder = config('system.storage.media.folder');

            $allThreatDefinitions = ThreatDefinition::query()
                ->select('id', 'image', 'name', 'short_description')
                ->get();
            $languages = Locale::listAvailable();

            foreach ($languages as $lang => $label) {
                $threatsArray = [];
                foreach ($allThreatDefinitions as $threat) {
                    $threat->translateContext($lang);
                    $threatsArray[] = [
                        'id' => $threat->id,
                        'name' => $threat->name,
                        'image' => $threat->image,
                        'short_description' => $threat->short_description,
                    ];
                }
                // fileName is the endpoint in the CDN
                $fileName = "l/" . $lang . "/threats.json";
                $uploader->uploadJson(
                    $threatsArray,
                    $fileName
                );
            }

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
        });
    }

}
