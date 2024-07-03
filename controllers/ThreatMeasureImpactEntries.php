<?php namespace Pensoft\Restcoast\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Pensoft\Restcoast\Models\ThreatMeasureImpactEntry;

class ThreatMeasureImpactEntries extends Controller
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
            'Pensoft.Restcoast',
            'threat-measure-impact-entries',
            'threat-measure-impact-entries'
        );
    }

}
