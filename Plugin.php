<?php

namespace Pensoft\RestcoastMobileApp;

use Config;
use Event;
use Pensoft\RestcoastMobileApp\Events\AppSettingsUpdated;
use Pensoft\RestcoastMobileApp\Events\MeasureDefinitionUpdated;
use Pensoft\RestcoastMobileApp\Events\SiteThreatImpactEntryUpdated;
use Pensoft\RestcoastMobileApp\Events\SiteUpdated;
use Pensoft\RestcoastMobileApp\Events\ThreatDefinitionUpdated;
use Pensoft\RestcoastMobileApp\Events\ThreatMeasureImpactEntryUpdated;
use Pensoft\RestcoastMobileApp\Listeners\HandleAppSettingsUpdated;
use Pensoft\RestcoastMobileApp\Listeners\HandleMeasureDefinitionUpdated;
use Pensoft\RestcoastMobileApp\Listeners\HandleSiteThreatImpactEntryUpdated;
use Pensoft\RestcoastMobileApp\Listeners\HandleSiteUpdate;
use Pensoft\RestcoastMobileApp\Listeners\HandleThreatDefinitionUpdated;
use Pensoft\RestcoastMobileApp\Listeners\HandleThreatMeasureImpactEntryUpdated;
use Pensoft\RestcoastMobileApp\Models\AppSettings;
use Pensoft\RestcoastMobileApp\Models\CustomAppSettings;
use Pensoft\RestcoastMobileApp\Services\SyncDataService;
use Superbalist\LaravelGoogleCloudStorage\GoogleCloudStorageServiceProvider;
use System\Classes\PluginBase;
use Validator;

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
        // Include vendor files
        $autoloadPath = __DIR__ . '/vendor/autoload.php';
        if (file_exists($autoloadPath)) {
            require $autoloadPath;
        }

        // Register 'gsc' disk
        \App::register(GoogleCloudStorageServiceProvider::class);

        $this->mediaFilesEvents();
        $this->syncDataEvents();
        $this->mergeConfig('cms');
        $this->mergeConfig('filesystems');

        // Add a new validation rule, so we can validate `mediafinder` fields
        Validator::extend('media_file_extension', function ($attribute, $value, $parameters, $validator) {
            if ($parameters[0] === 'image') {
                $parameters = ['jpg', 'png', 'gif', 'bmp', 'webp'];
            }
            // Convert parameters to lowercase to handle case-insensitivity
            $allowedExtensions = array_map('strtolower', $parameters);
            $filePath = storage_path('app/media' . $value);
            if (!file_exists($filePath)) {
                return false;
            }
            $extension = pathinfo($filePath, PATHINFO_EXTENSION);
            // Check if the file's extension is in the allowed list
            return in_array($extension, $allowedExtensions);
        }, 'The :attribute must be one of these file types: :values.');

        Validator::replacer(
            'media_file_extension',
            function ($message, $attribute, $rule, $parameters) {
                if ($parameters[0] === 'image') {
                    $parameters = ['jpg', 'png', 'gif', 'bmp', 'webp'];
                }
                return str_replace(
                    ':values',
                    implode(', ', $parameters),
                    $message
                );
            }
        );

        AppSettings::extend(function ($model) {
            $model->implement = array_diff(
                $model->implement,
                ['System.Behaviors.SettingsModel']
            );
            $model->extendClassWith(CustomAppSettings::class);
        });
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
                    'threat_definitions' => [
                        'label' => 'Threats Definitions',
                        'icon' => 'icon-crosshairs',
                        'url' => \Backend::url('pensoft/restcoastmobileapp/threatdefinitions'),
                        'permissions' => ['pensoft.restcoast.*'],
                    ],
                    'site_threat_impact_entries' => [
                        'label' => 'Site Threat Impact Entries',
                        'icon' => 'icon-crosshairs',
                        'url' => \Backend::url('pensoft/restcoastmobileapp/sitethreatimpactentries'),
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

    public function mediaFilesEvents()
    {
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

        Event::listen(
            'media.file.rename',
            function ($widget, $filePath, $newFilePath) {
                $syncDataService = new SyncDataService();
                if ($syncDataService->shouldSyncWithBucket($widget)) {
                    $syncDataService->syncMediaFile(
                        $filePath,
                        'rename',
                        $newFilePath
                    );
                }
            }
        );
    }

    public function syncDataEvents()
    {
        // Handle App Settings update
        Event::listen(
            AppSettingsUpdated::class,
            HandleAppSettingsUpdated::class
        );

        // Handle Site update
        Event::listen(
            SiteUpdated::class,
            HandleSiteUpdate::class
        );

        // Handle Threat definition update
        Event::listen(
            ThreatDefinitionUpdated::class,
            HandleThreatDefinitionUpdated::class
        );

        // Handle Site Threat Impact Entry update
        Event::listen(
            SiteThreatImpactEntryUpdated::class,
            HandleSiteThreatImpactEntryUpdated::class
        );

        // Handle Measure definition update
        Event::listen(
            MeasureDefinitionUpdated::class,
            HandleMeasureDefinitionUpdated::class
        );

        // Handle Threat Measure Impact Entry update
        Event::listen(
            ThreatMeasureImpactEntryUpdated::class,
            HandleThreatMeasureImpactEntryUpdated::class
        );
    }

    /**
     * @param $configKey
     * @return void
     */
    private function mergeConfig($configKey)
    {
        $pluginConfigFile = 'pensoft/restcoastmobileapp/config/' . $configKey . '.php';
        $pluginConfig = plugins_path($pluginConfigFile);
        // Merge CMS config
        if (file_exists($pluginConfig)) {
            $extraConfig = require $pluginConfig;
            $existingConfig = Config::get($configKey);
            if (($configKey === 'filesystems' && !array_key_exists('gcs', $existingConfig['disks']))
                || $configKey === 'cms') {
                $mergedConfig = array_merge_recursive($existingConfig, $extraConfig);
            } else {
                $mergedConfig = $existingConfig;
            }
            Config::set($configKey, $mergedConfig);
        }
    }
}
