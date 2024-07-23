<?php namespace Pensoft\RestcoastMobileApp\Models;

use Illuminate\Support\Facades\App;
use Model;
use October\Rain\Database\Traits\Validation;
use Pensoft\RestcoastMobileApp\Extensions\JsonableModel;
use System\Behaviors\SettingsModel;

/**
 * HomeSettings Model
 */
class AppSettings extends Model
{
    use Validation;

    public $implement = [
        SettingsModel::class,
        '@RainLab.Translate.Behaviors.TranslatableModel',
        JsonableModel::class
    ];
    public $settingsCode = 'pensoft_restcoast_settings';
    public $settingsFields = 'fields.yaml';


    /**
     * @var string table associated with the model
     */
    public $table = 'rcm_settings';

    /**
     * @var array guarded attributes aren't mass assignable
     */
    protected $guarded = ['*'];

    /**
     * @var array fillable attributes are mass assignable
     */
    protected $fillable = [];

    /**
     * @var array rules for validation
     */
    public $rules = [];

    /**
     * @var array Attributes to be cast to native types
     */
    protected $casts = [];

    /**
     * @var array jsonable attribute names that are json encoded and decoded from the database
     */
    protected $jsonable = [];

    /**
     * @var array appends attributes to the API representation of the model (ex. toArray())
     */
    protected $appends = [];

    /**
     * @var array hidden attributes removed from the API representation of the model (ex. toArray())
     */
    protected $hidden = [];

    /**
     * @var array dates attributes that should be mutated to dates
     */
    protected $dates = [
        'created_at',
        'updated_at'
    ];

    /**
     * @var array hasOne and other relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    public $translatable = ['privacy_policy', 'about', 'eu_disclaimer'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = 'rcm_settings';
    }

    public function generateJson()
    {
        $translationService = App::make("TranslationService");
        $sites              = $translationService->getAllWithTranslations(Site::class);
        $threats            = $translationService->getAllWithTranslations(Threat::class);

        return [
            'data' => [
                'countriesLayer' => $this->home_map_kml_layer,
                'mapStyle'       => $this->home_map_style,
                'sites'          => array_map(function ($site) {
                    return [
                        'id'           => $site->id,
                        'name'         => $site->name,
                        'coordinates'  => [
                            'lat'  => $site->coordinates_lat,
                            'long' => $site->coordinates_lon,
                        ],
                        'location'     => $site->location,
                        'scale'        => $site->scale,
                        'imageGallery' => array_values($site->image_gallery),
                    ];
                }, $sites[$this->translateContext()]),
                'threats'        => array_map(function ($threat) {
                    return [
                        'code'       => $threat->code,
                        'name'       => $threat->name,
                        'thumbnail'  => $threat->thumbnail,
                        'definition' => $threat->definition,
                    ];
                },
                    $threats[$this->translateContext()]),
            ]
        ];
    }
}
