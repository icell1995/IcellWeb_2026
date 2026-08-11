<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::before(function($user, $ability) {
          if ($user->hasPermission($ability)) {
                return true;
          }
    	});

	Gate::define('viewPulse', function (User $user) {
            if (!empty($user->role_id) && $user->role_id == 1) {
                return true;
            }

            return false;
        });

        //
          /* define a admin user role */
        // Gate::define('manage-users', function($user) {
        //     // return $user->role_id == '1';
        //     return $user->role_id==in_array(['1','2']);
        //  });

        //   Gate::define('manage-users', function($user) {
        //     // return $user->role_id == '1';
        //     return $user->role_id==('1');
        //  });
        
        //  Gate::define('manage-users', function($user) {
        //     // return $user->role_id == '1';
        //     return $user->role_id==('2');
        //  });
        //  /* define a admin polda user role */
        //  Gate::define('2', function($user) {
        //      return $user->role_id == '2';
        //  });
       
        //  /* define a admin polres role */
        //  Gate::define('3', function($user) {
        //      return $user->role_id == '3';
        //  });

        //  Gate::define('4', function($user) {
        //     return $user->role_id == '4';
        // });

        // Gate::define('5', function($user) {
        //     return $user->role_id == '5';
        // });


        // Gate::define('manage-users', function ($user) {
        //     return $user->hasAccess(['manage-users']);
        // });
        // Gate::define('update-post', function ($user, Post $post) {
        //     return $user->hasAccess(['update-post']) or $user->id == $post->user_id;
        // });
        // Gate::define('publish-post', function ($user) {
        //     return $user->hasAccess(['publish-post']);
        // });
        // Gate::define('see-all-drafts', function ($user) {
        //     return $user->inRole('editor');
        // });


    }
}
