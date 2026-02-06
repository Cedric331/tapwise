<?php

namespace App\Console\Commands;

use App\Mail\TrialEndingSoon;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendTrialEndingSoonEmails extends Command
{
    protected $signature = 'trial:ending-soon';

    protected $description = 'Send trial ending soon emails';

    public function handle(): int
    {
        $start = now()->addDays(2)->startOfDay();
        $end = now()->addDays(2)->endOfDay();

        $users = User::query()
            ->whereNotNull('trial_ends_at')
            ->whereBetween('trial_ends_at', [$start, $end])
            ->get();

        $sent = 0;
        foreach ($users as $user) {
            if ($user->hasAnyActiveSubscription()) {
                continue;
            }

            $daysLeft = max(now()->diffInDays($user->trial_ends_at, false), 0);
            Mail::to($user->email)->send(new TrialEndingSoon($user, $daysLeft));
            $sent++;
        }

        $this->info("Sent {$sent} trial ending soon emails.");

        return Command::SUCCESS;
    }
}
