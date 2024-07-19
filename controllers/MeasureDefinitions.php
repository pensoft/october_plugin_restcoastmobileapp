<?php namespace Pensoft\RestcoastMobileApp\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class MeasureDefinitions extends Controller
{
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\FormController',
    ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext(
            'Pensoft.RestcoastMobileApp',
            'restcoast',
            'measure_definitions'
        );
    }

    public function index()
    {
        // Ensure this action is correctly set up to handle the request
        $this->asExtension('ListController')->index();
    }
}
