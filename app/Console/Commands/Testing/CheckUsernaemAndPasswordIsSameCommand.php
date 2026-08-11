<?php

namespace App\Console\Commands\Testing;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CheckUsernaemAndPasswordIsSameCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:usernaem-and-password-is-same';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::select('id', 'username', 'password', 'phone')->limit(100000)->get();
        $countSame = 0;
        foreach ($users as $user) {
            $username = $user->username;
            $password = $user->password;
            $phone = $user->phone;

            if (Hash::check($username . '-', $password)) {
                DB::beginTransaction();
                try {
                    $user->update(['password' => Hash::make($username . '#_#' . $username)]);
                    DB::commit();
                    $countSame++;
                    $this->error("Password is same with username: " . $username);
                } catch (\Exception $e) {
                    DB::rollback();
                    throw $e;
                }
            }else{
                $this->info("Password is not same with username: " . $username);
            }
        }

        $this->info("Total same: " . $countSame);
    }
}
