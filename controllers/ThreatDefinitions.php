<?php namespace Pensoft\Restcoast\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Pensoft\Restcoast\Services\JsonUploader;

class ThreatDefinitions extends Controller
{
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\FormController',
    ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public function __construct(JsonUploader $uploader)
    {
        parent::__construct();
        BackendMenu::setContext(
            'Pensoft.Restcoast',
            'threat-definitions',
            'threat-definitions'
        );
    }

}
