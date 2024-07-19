<?php

namespace Pensoft\RestcoastMobileApp;

use Event;
use Illuminate\Support\Facades\App;
use Pensoft\RestcoastMobileApp\Events\SiteUpdated;
use Pensoft\RestcoastMobileApp\Events\ThreatDefinitionUpdated;
use Pensoft\RestcoastMobileApp\listeners\HandleSiteUpdated;
use Pensoft\RestcoastMobileApp\listeners\HandleThreatDefinitionUpdated;
use Pensoft\RestcoastMobileApp\Services\JsonGenerator;
use Pensoft\RestcoastMobileApp\Services\JsonUploader;
use Pensoft\RestcoastMobileApp\Services\SyncDataService;
use Pensoft\RestcoastMobileApp\Services\TranslationService;
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
        App::bind("TranslationService", TranslationService::class);
        App::bind("JsonUploader", JsonUploader::class);
        App::bind("JsonGenerator", JsonGenerator::class);
        App::bind("SyncDataService", SyncDataService::class);

        // Handle Site update
        Event::listen(
            SiteUpdated::class,
            HandleSiteUpdated::class
        );
        // Handle Threat definition update
        Event::listen(
            ThreatDefinitionUpdated::class,
            HandleThreatDefinitionUpdated::class
        );

        // Handle the uploading of media files
        Event::listen(
            'media.file.upload',
            function ($widget, $filePath) {
                $syncDataService = new SyncDataService();
                if ($syncDataService->shouldSyncWithBucket($widget)) {
                    $syncDataService->syncMediaFile(
                        $filePath,
                        'upload'
                    );
                }
            }
        );

        // Handle the deletion of media files
        Event::listen(
            'media.file.delete',
            function ($widget, $filePath) {
                $syncDataService = new SyncDataService();
                if ($syncDataService->shouldSyncWithBucket($widget)) {
                    $syncDataService->syncMediaFile(
                        $filePath,
                        'delete'
                    );
                }
            }
        );

    }

    public function registerNavigation()
    {

        return [
            'restcoast' => [
                'label' => 'Restcoast Content',
                'url' => \Backend::url('pensoft/restcoastmobileapp'),
                'icon' => 'icon-picture-o',
                'permissions' => ['pensoft.restcoast.*'],
                'order' => 500,

                'sideMenu' => [
                    'sites' => [
                        'label' => 'Sites',
                        'icon' => 'icon-map-marker',
                        'url' => \Backend::url('pensoft/restcoastmobileapp/sites'),
                        'permissions' => ['pensoft.restcoast.*'],
                    ],
                    'site_threat_impact_entries' => [
                        'label' => 'Site Threat Impact Entries',
                        'icon' => 'icon-crosshairs',
                        'url' => \Backend::url('pensoft/restcoastmobileapp/sitethreatimpactentries'),
                        'permissions' => ['pensoft.restcoast.*'],
                    ],
                    'threat_definitions' => [
                        'label' => 'Threats Definitions',
                        'icon' => 'icon-crosshairs',
                        'url' => \Backend::url('pensoft/restcoastmobileapp/threatdefinitions'),
                        'permissions' => ['pensoft.restcoast.*'],
                    ],
                    'measure_definitions' => [
                        'label' => 'Measures Definitions',
                        'icon' => 'icon-crosshairs',
                        'url' => \Backend::url('pensoft/restcoastmobileapp/measuredefinitions'),
                        'permissions' => ['pensoft.restcoast.*'],
                    ],
                    'threat_measure_impact_entries' => [
                        'label' => 'Threat Measure Impact Entries',
                        'icon' => 'icon-crosshairs',
                        'url' => \Backend::url('pensoft/restcoastmobileapp/threatmeasureimpactentries'),
                        'permissions' => ['pensoft.restcoast.*'],
                    ],
                    'app_settings' => [
                        'label' => 'App Settings',
                        'url' => \Backend::url('pensoft/restcoastmobileapp/appsettings'),
                        'icon' => 'icon-cog',
                        'permissions' => ['pensoft.restcoast.access_settings'],
                        'order' => 500,
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
            'pensoft.restcoast.manage_threat_definitions' => [
                'tab' => 'Threats',
                'label' => 'Manage Threat Definitions'
            ],
            'pensoft.restcoast.manage_site_threat_impact_entries' => [
                'tab' => 'Threats',
                'label' => 'Manage Site Threat Impact Entries'
            ],
            'pensoft.restcoast.manage_threat_measure_impact_entries' => [
                'tab' => 'Threats',
                'label' => 'Manage Threat Measure Impact Entries'
            ],
            'pensoft.restcoast.manage_measure_definitions' => [
                'tab' => 'Measure Definitions',
                'label' => 'Manage Measure Definitions'
            ],
            'pensoft.restcoast.access_settings' => [
                'label' => 'Access Settings',
                'tab' => 'Settings',
            ],
        ];
    }
}
