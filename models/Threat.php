<?php
namespace Pensoft\RestcoastMobileApp\Models;

use Illuminate\Support\Facades\App;
use Model;
use Pensoft\RestcoastMobileApp\Extensions\JsonableModel;

class Threat extends Model
{
    use \October\Rain\Database\Traits\Validation;

    public $table = 'pensoft_restcoast_threats';


    public $rules = [
        'name'        => 'required',
        'description' => 'required',
    ];

    public $translatable = [
        'name',
        'description',
        'short_description',
        'content_blocks',
    ];

    public $jsonable = ['content_blocks'];

    public $implement = [
        '@RainLab.Translate.Behaviors.TranslatableModel',
        JsonableModel::class
    ];

    protected $fillable = [
        'name',
        'description',
        'short_description',
        'content_blocks',
    ];

    public $belongsToMany = [
        'sites' => [
            Site::class,
            'table' => 'pensoft_restcoast_site_threat',
            'order' => 'name'
        ]
    ];

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($model) {
            $uploader = App::make("JsonUploader");

            $sitesJson = Site::query()->with('threats')->get()->toJson();
            $uploader->uploadJson($sitesJson, 'sites.json');
        });
    }
}
