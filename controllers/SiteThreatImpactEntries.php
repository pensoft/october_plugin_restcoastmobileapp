<?php namespace Pensoft\RestcoastMobileApp\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Pensoft\RestcoastMobileApp\Models\SiteThreatImpactEntry;

class SiteThreatImpactEntries extends Controller
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
            'site_threat_impact_entries'
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

            // Check if the form is in the context of a repeater field
            if ($widget->isNested) {
                return;
            }

            $model = $widget->model;
            // Add a custom message field if no Measure Impact entries are available
            if (!count($model->measure_impact_entries)) {
                $widget->removeField('_hint1');
                $widget->removeField('outcomes');
                $widget->removeField('measure_impact_entries');

                $widget->addFields([
                    'no_measures_message' => [
                        'label' => '',
                        'type' => 'partial',
                        'path' => '$/pensoft/restcoastmobileapp/partials/_no_measure_impact_entries_message.htm',
                        'span' => 'full'
                    ]
                ]);
            }
        });
    }

}
