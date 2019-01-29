<?php

namespace App\Mail;

use App\Thread;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifySubscribed extends Mailable
{
    use Queueable, SerializesModels;

    public $thread;
    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Thread $thread, User $user)
    {
        $this->thread = $thread;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.notifySubscribed');
    }
}
