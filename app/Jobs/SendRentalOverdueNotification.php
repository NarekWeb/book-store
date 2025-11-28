<?php

namespace App\Jobs;

use App\Mail\RentalOverdueMail;
use App\Models\BookRental;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendRentalOverdueNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $queue = 'mail';

    public function __construct(
        public int $rentalId
    ) {}

    public function handle(): void
    {
        $rental = BookRental::with(['user', 'book'])->find($this->rentalId);

        if (
            ! $rental ||
            $rental->returned_at ||
            $rental->status !== 'active' ||
            $rental->due_date >= now()
        ) {
            return;
        }

        if ($rental->user && $rental->user->email) {
            Mail::to($rental->user->email)->send(
                new RentalOverdueMail($rental)
            );
        }

        $rental->forceFill([
            'status'           => 'overdue',
            'last_notified_at' => now(),
        ])->save();
    }
}
