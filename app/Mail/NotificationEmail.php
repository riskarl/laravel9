<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $details;
    public $pdfData;
    public $fileName;

    public function __construct($details, $pdfData = null, $fileName = null)
    {
        $this->details = $details;
        $this->pdfData = $pdfData;
        $this->fileName = $fileName;
    }

    public function build()
    {
        // Mengambil base URL aplikasi secara dinamis
        $this->details['base_url'] = url('/');

        // Tentukan tampilan yang akan digunakan berdasarkan apakah ada file yang di-attach
        $viewName = 'emails.notification';
        if ($this->pdfData && $this->fileName) {
            $viewName = 'emails.approved';
        } else if (isset($this->details['proposal_title']) && stripos($this->details['proposal_title'], 'LPJ') !== false) {
            $viewName = 'emails.notification-lpj';
        }

        // Membangun email
        $email = $this->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
                      ->subject('Pemberitahuan Berkas Baru')
                      ->view($viewName)
                      ->with('details', $this->details);

        // Lampirkan data PDF jika ada
        if ($this->pdfData && $this->fileName) {
            $email->attachData($this->pdfData, $this->fileName, [
                'mime' => 'application/pdf',
            ]);
        }

        return $email;
    }
}
