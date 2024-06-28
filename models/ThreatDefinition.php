<?php namespace Pensoft\Restcoast\Models;

use Model;

class Threat extends Model
{
    use \October\Rain\Database\Traits\Validation;

    public $table = 'pensoft_restcoast_threats';


    public $rules = [
        'name' => 'required',
        'description' => 'required',
    ];

    public $translatable = [
        'name',
        'description',
        'short_description',
        'content_blocks',
        'measures[pivot][content]'
    ];

    public $jsonable = ['content_blocks'];

    public $implement = ['@RainLab.Translate.Behaviors.TranslatableModel'];

    protected $fillable = [
        'name',
        'description',
        'short_description',
        'content_blocks',
        'measures'
    ];


    public $belongsToMany = [
//        'sites' => [
//            Site::class,
//            'table' => 'pensoft_restcoast_site_threat',
//            'order' => 'name'
//        ],
        'measures' => [
            Measure::class,
            'table' => 'pensoft_restcoast_measure_threat',
            'pivot' => ['content'],
            'timestamps' => true,
            'pivotModel' => ThreatMeasurePivot::class,
        ]
    ];

    protected static function boot()
    {
        parent::boot();
//        $uploader = new JsonUploader();
//        static::updating(function($model) use ($uploader) {
//            $englishJsonContent = $model->toJson();
//            $uploader->uploadJson($englishJsonContent, 'en/threats/threat-1.json');
//            $model->translateContext('es');
//            $spanishJsonContent = $model->toJson();
//            $uploader->uploadJson($spanishJsonContent, 'es/threats/threat-1.json');
//        });
    }

//    public function getMeasures($model)
//    {
//        return Measure::all()->pluck('name', 'id')->toArray();
//    }

}
