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

        Gate::before(function ($user, $ability) {
            // Aturan khusus Polda Level 3 (ADMIN SATKER): Tidak boleh akses dokumen approval
            if ($user && $user->role_id == 3 && !empty($user->polda_id) && (empty($user->polres_id) || $user->polres_id == 0)) {
                if (str_starts_with($ability, 'document-approval')) {
                    return false;
                }
            }

            if ($user && $user->hasPermission($ability)) {
                return true;
            }
        });

        // Menu / route `can:productivity.R`: izin baca modul atau izin LP (matrix terpisah)
        Gate::define('productivity.R', function (?User $user) {
            if (!$user) {
                return false;
            }

            return $user->hasPermission('productivity.R')
                || $user->hasPermission('productivity-lp.R');
        });

        // Struktur organisasi: matrix punya R dan D
        Gate::define('organization.R', function (?User $user) {
            if (!$user) {
                return false;
            }

            return $user->hasPermission('organization.R')
                || $user->hasPermission('organization.D');
        });

        Gate::define('viewPulse', function (User $user) {
            return $user->hasPermission('pulse.R');
        });

        Gate::define('esign-document', function (User $user) {
            $officer = $user->officer;
            if (!$officer || $officer->class !== 'SIGNATORY' || !isset($officer->position->positionCluster)) {
                return false;
            }

            $positionCluster = $officer->position->positionCluster;
            return $positionCluster->is_can_signatory == true 
                && isset($officer->position->is_can_signatory) 
                && $officer->position->is_can_signatory == true;
        });

        Gate::define('can-entry-document', function (User $user) {
            if ($user->role_id == 3 && !empty($user->polres_id) && $user->polres_id != 0) {
                return (bool)($user->properties['is_can_entry_document'] ?? false);
            }
            return true;
        });

        Gate::define('access-case-participants', function (User $user) {
            return $user->hasPermission('case.R') || $user->hasPermission('productivity.R');
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
