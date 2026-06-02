<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotifyPI extends Mailable
{
    use Queueable, SerializesModels;

    public $reservation;
    public $researcher;

    public function __construct($reservation, $researcher)
    {
        $this->reservation = $reservation;
        $this->researcher = $researcher;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Researcher #{$this->researcher->user_id} Submitted Reservation #{$this->reservation->id}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'email.pi',
        );
    }
}