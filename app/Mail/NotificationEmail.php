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

        // Tentukan tampilan yang akan digunakan berdasarkan apakah ada file yang di-attach
        $viewName = 'emails.notification';
        if (isset($this->details['file_attachment']) && !empty($this->details['file_attachment'])) {
            $viewName = 'emails.approved';
        } else if (isset($this->details['proposal_title']) && stripos($this->details['proposal_title'], 'LPJ') !== false) {
            $viewName = 'emails.notification-lpj';
        }

        // Membangun email
        $email = $this->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
                      ->subject('Pemberitahuan Berkas Baru')
                      ->view($viewName)
                      ->with('details', $this->details);

        // Lampirkan file jika ada
        if (isset($this->details['file_attachment']) && !empty($this->details['file_attachment'])) {
            $filePath = $this->details['file_attachment'];
            $email->attach($filePath, [
                'as' => basename($filePath),
                'mime' => mime_content_type($filePath),
            ]);
        }

        return $email;
    }
}
