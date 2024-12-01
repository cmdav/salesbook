<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Email\EmailService; // Make sure to import the EmailService
use App\Models\User; // Import the User model if you need to get user info
use Carbon\Carbon;


use App\Models\SubscriptionStatus;

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
    protected $description = 'Send subscription reminder email to users';

    /**
     * Execute the console command.
     */
    public function handle()
    {


        $expiryReminderDate = Carbon::now(); // Current date and time

        $endDate = Carbon::now()->addDays(7); // Date 7 days from now

        // Query subscriptions expiring within the next 7 days
        $subscriptions = SubscriptionStatus::with(['organization'])
            ->whereBetween('end_time', [$expiryReminderDate, $endDate])
            ->get();

        if ($subscriptions->isEmpty()) {
            $this->info("No subscriptions are expiring on {$expiryReminderDate}.");
            return;
        }

        foreach ($subscriptions as $subscription) {
            $organization = $subscription->organization;

            if ($organization && isset($organization->company_email)) {

                // Log to confirm that the cron job is running
                \Log::info("Cron is working fine!");

                \Log::info($organization);

                $emailType = 'subscription_reminder';
                $otherDetail = [
                    "msg" => " <strong>{$organization->organization_name}</strong>, your subscription will expire on {$subscription->end_time}. Please renew promptly to avoid service disruption",

                ];


                $emailSent = EmailService::sendEmail(["email" => $organization->company_email], $emailType, $otherDetail);

                if ($emailSent) {
                    \Log::info("Subscription reminder email sent successfully to {$organization->company_email}");
                } else {
                    \Log::error("Failed to send subscription reminder email to {$organization->company_email}");
                }
            }
        }


    }
}
