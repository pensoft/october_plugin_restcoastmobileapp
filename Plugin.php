<?php namespace Pensoft\RestcoastMobileApp;

use Backend;
use System\Classes\PluginBase;

/**
 * RestcoastMobileApp Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'RestcoastMobileApp',
            'description' => 'No description provided yet...',
            'author'      => 'Pensoft',
            'icon'        => 'icon-leaf'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Boot method, called right before the request route.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return []; // Remove this line to activate

        return [
            'Pensoft\RestcoastMobileApp\Components\MyComponent' => 'myComponent',
        ];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return []; // Remove this line to activate

        return [
            'pensoft.restcoastmobileapp.some_permission' => [
                'tab' => 'RestcoastMobileApp',
                'label' => 'Some permission'
            ],
        ];
    }

    /**
     * Registers back-end navigation items for this plugin.
     *
     * @return array
     */
    public function registerNavigation()
    {
        return []; // Remove this line to activate

        return [
            'restcoastmobileapp' => [
                'label'       => 'RestcoastMobileApp',
                'url'         => Backend::url('pensoft/restcoastmobileapp/mycontroller'),
                'icon'        => 'icon-leaf',
                'permissions' => ['pensoft.restcoastmobileapp.*'],
                'order'       => 500,
            ],
        ];
    }
}
