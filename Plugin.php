<?php namespace Pensoft\NewsRSS;

use Backend;
use System\Classes\PluginBase;
use Route;
use Pensoft\NewsRSS\Components\RSSFeed;

/**
 * NewsRSS Plugin Information File
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
            'name'        => 'NewsRSS',
            'description' => 'Generates an RSS feed for the news section.',
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
        // Register the route for serving the RSS feed
        Route::get('news/rss', function () {
            return (new RSSFeed)->onRun();
        }); 
    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return [
            'Pensoft\NewsRSS\Components\RSSFeed' => 'rssFeed',
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
            'pensoft.newsrss.some_permission' => [
                'tab' => 'NewsRSS',
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
            'newsrss' => [
                'label'       => 'NewsRSS',
                'url'         => Backend::url('pensoft/newsrss/mycontroller'),
                'icon'        => 'icon-leaf',
                'permissions' => ['pensoft.newsrss.*'],
                'order'       => 500,
            ],
        ];
    }
}
