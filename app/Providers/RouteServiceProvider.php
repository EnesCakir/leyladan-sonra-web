<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to the controller routes in your routes file.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function boot()
    {
        parent::boot();

        Route::bind('any_user', function($id) {
            return User::withoutGlobalScopes()->findOrFail($id);
        });

    }

    /**
     * Define the routes for the application.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function map()
    {
      $this->mapApiRoutes();
      $this->mapAdminRoutes();
      $this->mapWebRoutes();

    }

    /**
 * Define the "web" routes for the application.
 *
 * These routes all receive session state, CSRF protection, etc.
 *
 * @return void
 */

  protected function mapWebRoutes()
  {
      Route::group([
          'middleware' => 'web',
          'namespace'  => $this->namespace,
      ], function ($router) {
          require base_path('routes/web.php');
      });
  }

  /**
 * Define the "admin" routes for the application.
 *
 * These routes all receive session state, CSRF protection, etc.
 *
 * @return void
 */

  protected function mapAdminRoutes()
  {
      Route::group([
          'middleware' => 'web',
          'namespace'  => $this->namespace,
          'prefix'     => 'admin',
          'as'         => 'admin.',
      ], function ($router) {
          require base_path('routes/admin.php');
      });
  }

  /**
   * Define the "api" routes for the application.
   *
   * These routes are typically stateless.
   *
   * @return void
   */
  protected function mapApiRoutes()
  {
      Route::group([
          'middleware' => 'api',
          'namespace'  => $this->namespace,
          'prefix'     => 'api',
      ], function ($router) {
          require base_path('routes/api.php');
      });
  }

}
