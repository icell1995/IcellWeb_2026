<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    // protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware(['web', 'auth', 'is-administrator', 'prevent-back-history'])
                ->namespace($this->namespace)
                ->prefix('cms')
                ->group(base_path('routes/cms.php'));

            Route::middleware(['web'])
                ->namespace($this->namespace)
                ->prefix('lms')
                ->group(base_path('routes/lms.php'));

            Route::middleware('api-auth')
                ->prefix('icell-services/api-emp-bareskrim/v2')
                ->namespace($this->namespace)
                ->group(base_path('routes/icell-services/api-emp-bareskrim/v2.php'));
       
            Route::middleware('api-auth')
                ->prefix('icell-services/api-emp-bareskrim/v1')
                ->namespace($this->namespace)
                ->group(base_path('routes/icell-services/api-emp-bareskrim/v1.php'));
          
            Route::middleware('api-auth')
                ->prefix('icell-services/api-irsms-korlantas/v1')
                ->namespace($this->namespace)
                ->group(base_path('routes/icell-services/api-irsms-korlantas/v1.php'));

            Route::middleware('api-auth')
                ->prefix('icell-services/api-tar-korlantas/v1')
                ->namespace($this->namespace)
                ->group(base_path('routes/icell-services/api-tar-korlantas/v1.php'));
            
            Route::middleware('api-auth')
                ->prefix('icell-services/api-divtik-polri')
                ->namespace($this->namespace)
                ->group(base_path('routes/icell-services/api-divtik-polri/get-divtik.php'));

            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api/api.php'));
                
            Route::prefix('api/emp')
                ->middleware('api-auth')
                ->namespace($this->namespace)
                ->group(base_path('routes/api/emp-api.php'));
                
            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));

            Route::middleware(['web', 'auth', 'prevent-back-history', 'can:case.R'])
                ->namespace($this->namespace)
                ->prefix('case')
                ->group(base_path('routes/case.php'));

            Route::middleware(['web', 'auth', 'prevent-back-history', 'can:productivity.R'])
                ->namespace($this->namespace)
                ->prefix('produktivitas')
                ->group(base_path('routes/produktivitas.php'));
            
            Route::middleware(['web', 'auth', 'prevent-back-history', 'can:productivity.R'])
                ->namespace($this->namespace)
                ->prefix('document')
                ->group(base_path('routes/document.php'));
            
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
