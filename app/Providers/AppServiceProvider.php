<?php

namespace Ensphere\Ensphere\Providers;

use Illuminate\Support\ServiceProvider;
use EnsphereCore\Libs\Config\Publish;
use EnsphereCore\Libs\Providers\Service;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Determines whether running as module or application
     *
     * @return bool
     */
	private function isModule()
	{
		return file_exists( __DIR__ . "/../../../../../vendor" );
	}

	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		/** Adding the location not a named space so it can check the application first then the module */
		view()->addLocation( __DIR__ . '/../../resources/views' );
		if( $this->isModule() ) {
			$this->publishes( Publish::bower([
				__DIR__ . '/../../public/package/ensphere/ensphere/' => base_path( 'public/package/ensphere/ensphere/' ),
				__DIR__ . '/../../resources/database/migrations/' => database_path( 'migrations/vendor/ensphere/ensphere/' )
			], __DIR__ ), 'forced' );
		}
	}

	/**
	 * THESE ARE APPLICATION CONTRACTS.
	 * REGISTER MODULE CONTRACTS IN THE REGISTRATION FILE SO THEY CAN BE EXTENDED PER APPLICATION
     *
     * @return void
	 */
	public function register()
	{
		if( ! $this->isModule() ) {
			$contracts = Service::contracts([

				/** THESE ARE APPLICATION CONTRACTS. */
                "Ensphere\\Ensphere\\Contracts\\Blueprints\\ApiBlueprint" => "Ensphere\\Ensphere\\Contracts\\Api"

			]);
			foreach( $contracts as $blueprint => $contract ) {
				$this->app->singleton( $blueprint, $contract );
			}
		}
	}

}
