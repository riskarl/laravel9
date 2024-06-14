<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $details;

    public function __construct($details)
    {
        $this->details = $details;
    }

    public function build()
    {
        // Mengambil base URL aplikasi secara dinamis
        $this->details['base_url'] = url('/');

        return $this->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
                    ->subject('Pemberitahuan Proposal Baru')
                    ->view('emails.notification')
                    ->with('details', $this->details);
    }
}
