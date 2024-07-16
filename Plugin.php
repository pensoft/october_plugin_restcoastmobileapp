<?php

namespace Pensoft\Restcoast;

use Model;
use Pensoft\Restcoast\Extensions\JsonableModel;
use Pensoft\Restcoast\Services\JsonGenerator;
use Pensoft\Restcoast\Services\JsonUploader;
use Pensoft\Restcoast\Services\TranslationService;
use System\Classes\PluginBase;
use Illuminate\Support\Facades\App;

class Plugin extends PluginBase {
    public function pluginDetails() {
        return [
            'name'        => 'Reastcoast App',
            'description' => 'Provides sites/threats management features.',
            'author'      => 'Pensoft',
            'icon'        => 'icon-mountain'
        ];
    }

    public function boot() {
        App::bind( "TranslationService", TranslationService::class );
        App::bind( "JsonUploader", JsonUploader::class );
        App::bind( "JsonGenerator", JsonGenerator::class );
    }

    public function registerNavigation() {

        return [
            'user' => [
                'label'       => 'Restcoast Content',
                'url'         => \Backend::url( 'pensoft/restcoast/sites' ),
                'icon'        => 'icon-picture-o',
                'permissions' => [ 'pensoft.restcoast.*' ],
                'order'       => 500,

                'sideMenu' => [
                    'users'    => [
                        'label'       => 'Sites',
                        'icon'        => 'icon-map-marker',
                        'url'         => \Backend::url( 'pensoft/restcoast/sites' ),
                        'permissions' => [ 'pensoft.restcoast.*' ],
                    ],
                    'threats'  => [
                        'label'       => 'Threats',
                        'icon'        => 'icon-crosshairs',
                        'url'         => \Backend::url( 'pensoft/restcoast/threats' ),
                        'permissions' => [ 'pensoft.restcoast.*' ],
                    ],
                    'settings' => [
                        'label'       => 'App Settings',
                        'url'         => \Backend::url( 'pensoft/restcoast/appsettings' ),
                        'icon'        => 'icon-cog',
                        'permissions' => [ 'pensoft.restcoast.access_settings' ],
                        'order'       => 500,
                    ]
                ]
            ]
        ];

    }

    public function registerPermissions() {
        return [
            'pensoft.restcoast.manage_sites'    => [
                'tab'   => 'Sites',
                'label' => 'Manage sites'
            ],
            'pensoft.restcoast.manage_threats'  => [
                'tab'   => 'Threats',
                'label' => 'Manage threats'
            ],
            'pensoft.restcoast.access_settings' => [
                'label' => 'Access Settings',
                'tab'   => 'Settings',
            ],
        ];
    }
}
