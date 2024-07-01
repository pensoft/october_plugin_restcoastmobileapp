<?php namespace Pensoft\Restcoast\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Pensoft\Restcoast\Models\Site;
use Pensoft\Restcoast\Models\ThreatDefinition;
use Pensoft\Restcoast\Services\JsonUploader;

class MeasureDefinitions extends Controller
{
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\FormController',
        'Backend\Behaviors\RelationController',
    ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';
    public $relationConfig = 'config_relation.yaml';

    public function __construct(JsonUploader $uploader)
    {
        parent::__construct();
        BackendMenu::setContext(
            'Pensoft.Restcoast',
            'measure-definitions',
            'measure-definitions'
        );
    }

}
