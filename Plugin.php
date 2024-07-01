<?php namespace Pensoft\Restcoast;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function pluginDetails()
    {
        return [
            'name' => 'Reastcoast App',
            'description' => 'Provides sites/threats management features.',
            'author' => 'Pensoft',
            'icon' => 'icon-mountain'
        ];
    }

    public function boot()
    {
        // Optional: You can add any boot logic here if needed
    }

    public function registerNavigation()
    {

        return [
            'user' => [
                'label' => 'Restcoast Content',
                'url' => \Backend::url('pensoft/restcoast/sites'),
                'icon' => 'icon-mountain',
                'permissions' => ['pensoft.restcoast.*'],
                'order' => 500,

                'sideMenu' => [
                    'sites' => [
                        'label' => 'Sites',
                        'icon' => 'icon-mountain',
                        'url' => \Backend::url('pensoft/restcoast/sites'),
                        'permissions' => ['pensoft.restcoast.*'],
                    ],
                    'site_threat_impact_entries' => [
                        'label' => 'Site Threat Impact Entries',
                        'icon' => 'icon-mountain',
                        'url' => \Backend::url('pensoft/restcoast/sitethreatimpactentries'),
                        'permissions' => ['pensoft.restcoast.*'],
                    ],
                    'threat_definitions' => [
                        'label' => 'Threats Definitions',
                        'icon' => 'triangle-exclamation',
                        'url' => \Backend::url('pensoft/restcoast/threatdefinitions'),
                        'permissions' => ['pensoft.restcoast.*'],
                    ],
                    'measures_definitions' => [
                        'label' => 'Measures Definitions',
                        'icon' => 'triangle-exclamation',
                        'url' => \Backend::url('pensoft/restcoast/measuredefinitions'),
                        'permissions' => ['pensoft.restcoast.*'],
                    ],
                    'threat_measure_impact_entries' => [
                        'label' => 'Threat Measure Impact Entries',
                        'icon' => 'triangle-exclamation',
                        'url' => \Backend::url('pensoft/restcoast/threatmeasureimpactentries'),
                        'permissions' => ['pensoft.restcoast.*'],
                    ]
                ]
            ]
        ];

    }

    public function registerPermissions()
    {
        return [
            'pensoft.restcoast.manage_sites' => [
                'tab' => 'Sites',
                'label' => 'Manage sites'
            ],
            'pensoft.restcoast.manage_threats' => [
                'tab' => 'Threats',
                'label' => 'Manage threats'
            ],
        ];
    }
}
