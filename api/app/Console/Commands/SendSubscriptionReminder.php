<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\SubscriptionStatus;
use Carbon\Carbon;

class SendSubscriptionReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-subscription-reminder';

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
        $expiryReminderDate = Carbon::now()->addDays(7)->toDateString();

        // Query subscriptions expiring in 7 days
        $subscriptions = SubscriptionStatus::with(['organization'])
           // ->where('end_time', $expiryReminderDate)
            ->get();

        if ($subscriptions->isEmpty()) {
            $this->info("No subscriptions are expiring on {$expiryReminderDate}.");
            return;
        }

        foreach ($subscriptions as $subscription) {
            $organization = $subscription->organization;

            if ($organization && isset($organization->email)) {
                // Send reminder email
                Mail::raw(
                    "Dear {$organization->name}, your subscription will expire on {$subscription->end_time}. Please renew promptly to avoid service disruption.",
                    function ($message) use ($organization) {
                        $message->to('okomemmanuel1@gmail.com')
                            ->subject('Subscription Expiry Reminder');
                    }
                );

                $this->info("Reminder sent to {$organization->email}");
            }
        }
    }
}
