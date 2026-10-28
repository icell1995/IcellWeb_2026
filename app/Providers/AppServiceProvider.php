<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Opcodes\LogViewer\Facades\LogViewer;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        LogViewer::auth(function ($request) {
            $user = Auth::user();

            if($user) {
                if($user->role_id == 1) {
                    return true;
                }
            }
            return false;
        });

        Paginator::useBootstrap();

        // Mencegah N+1 Query Problem di environment development/local
        Model::preventLazyLoading(! $this->app->isProduction());
        Model::preventSilentlyDiscardingAttributes(! $this->app->isProduction());

        if (!class_exists('SimpleSoftwareIO\QrCode\Facades\QrCode') && class_exists('F9WebLtd\QrCode\Facades\QrCode')) {
            class_alias('F9WebLtd\QrCode\Facades\QrCode', 'SimpleSoftwareIO\QrCode\Facades\QrCode');
        }

        if (!class_exists('Barryvdh\DomPDF\Facade') && class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
            class_alias('Barryvdh\DomPDF\Facade\Pdf', 'Barryvdh\DomPDF\Facade');
        }
    }
}
