<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DeleteExpiredOtps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-expired-otps';

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
        $deleted = \DB::table('tb_email_otps')
            ->where('expires_at', '<', now())
            ->delete();

        $this->info("Deleted {$deleted} expired OTP(s).");
    }
}
