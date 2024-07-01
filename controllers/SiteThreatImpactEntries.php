<?php namespace Pensoft\Restcoast\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Pensoft\Restcoast\Models\SiteThreatImpactEntry;
use Pensoft\Restcoast\Models\ThreatMeasureImpactEntry;

class SiteThreatImpactEntries extends Controller
{
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\FormController',
        'Backend\Behaviors\RelationController',
    ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';
    public $relationConfig = 'config_relation.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext(
            'Pensoft.Restcoast',
            'site-threat-impact-entries',
            'site-threat-impact-entries'
        );

        // Listen for the form.extendFields event to dynamically modify form fields
        $this->addDynamicFormFields();
    }

    protected function addDynamicFormFields()
    {
        // Use form.extendFields to modify the form
        \Event::listen('backend.form.extendFields', function ($widget) {
            // Only apply to the Site model and the controller that you are targeting
            if (!$widget->getController() instanceof SiteThreatImpactEntries) {
                return;
            }

            if (!$widget->model instanceof SiteThreatImpactEntry) {
                return;
            }

            $model = $widget->model;
            if (!count($model->measure_impact_entries)) {
                $widget->removeField('outcomes');
                $widget->removeField('measure_impact_entries');

                // Add a custom message field if no Measures are available
                $widget->addFields([
                    'no_measures_message' => [
                        'label' => 'Notice',
                        'type' => 'partial',
                        'path' => '$/pensoft/restcoast/partials/_no_measure_impact_entries_message.htm',
                        'span' => 'full'
                    ]
                ]);
            }
        });
    }

}
