<?php namespace Creitive\Breadcrumbs;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class BreadcrumbsServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
        if ($this->isLegacyLaravel() || $this->isOldLaravel())
        {
		    $this->package('creitive/breadcrumbs');
        }

        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        $loader->alias('Breadcrumbs', 'Creitive\Breadcrumbs\Facades\Breadcrumbs');

	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{

		$this->app['breadcrumbs'] = $this->app->share(function($app)
		{
			return new Breadcrumbs;
		});
	}

    public function isLegacyLaravel()
    {
      return Str::startsWith(Application::VERSION, array('4.1.', '4.2.'));
    }

    public function isOldLaravel()
    {
        return Str::startsWith(Application::VERSION, '4.0.');
    }

}
