<?php namespace Pensoft\Restcoast\Models;

use Model;
//use RainLab\Translate\Behaviors\TranslatableModel;

class Event extends Model
{
    use \October\Rain\Database\Traits\Validation;

    public $table = 'pensoft_restcoast_events';


    public $rules = [
        'name'      => 'required',
        'start_date'=> 'required|date',
        'end_date'  => 'required|date|after_or_equal:start_date',
        'location'  => 'required'
    ];

    public $translatable = ['name', 'location'];

//    public $implement = [TranslatableModel::class];

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'location'
    ];
}
