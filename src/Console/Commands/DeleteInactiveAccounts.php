<?php

namespace Sicaboy\LaravelSecurity\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class DeleteInactiveAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravel-security:delete-inactive-accounts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Laravel Security Delete Inactive Accounts';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Delete accounts with xxx days of no activity
        $config = config('laravel-security.password_policy.auto_delete_inactive_accounts');
        if(empty($config)) {
            $this->error("Invalid Config");
        }
        if ($config['enabled'] !== true) {
            $this->error("Disabled");
            return;
        }
        $days = $config['days_after_last_login'];
        $this->info("Deleting accounts with {$days} days of no activity");
        $modelClassName = config('laravel-security.database.user_security_model');
        $userExtends = $modelClassName::whereDate('last_loggein_at', '<', Carbon::today()->subDays($days))
            ->get();
        foreach ($userExtends as $userExtend) {
            $this->line("Deleting user: {$userExtend->user->email}");

            if($config['email_notification']['enabled'] == true) {
                Mail::send($config['email_notification']['template'], [
                    'user' => $userExtend->user,
                    'days' => $days,
                ], function($message) use ($userExtend, $config) {
                    $message->to($userExtend->user->email);
                    $message->subject($config['email_notification']['subject']);
                });
            }
            $userExtend->user->delete();
            $userExtend->delete();
        }
    }
}
