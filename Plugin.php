<?php namespace Pensoft\Restcoast;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function pluginDetails()
    {
        return [
            'name'        => 'Events',
            'description' => 'Provides event management features.',
            'author'      => 'Pensoft',
            'icon'        => 'icon-calendar'
        ];
    }

    public function boot()
    {
        // Optional: You can add any boot logic here if needed
    }

    public function registerNavigation()
    {
        return [
            'events' => [
                'label'       => 'Events',
                'url'         => \Backend::url('pensoft/restcoast/events'),
                'icon'        => 'icon-calendar',
                'permissions' => ['pensoft.restcoast.*'],
                'order'       => 500,
            ],
        ];
    }

    public function registerPermissions()
    {
        return [
            'pensoft.restcoast.manage_events' => [
                'tab' => 'Events',
                'label' => 'Manage events'
            ],
        ];
    }
}
