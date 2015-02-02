<?php namespace Creitive\Breadcrumbs;

use Illuminate\Support\ServiceProvider;

class BreadcrumbsServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('creitive/breadcrumbs');
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

		$this->app->alias('breadcrumbs', 'Creitive\Breadcrumbs\Breadcrumbs');
	}

}
