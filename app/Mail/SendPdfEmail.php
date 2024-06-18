<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendPdfEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $pathToPdf;
    public $details; // Variabel untuk menyimpan data yang akan di-pass ke view

    /**
     * Create a new message instance.
     *
     * @param string $pathToPdf Path to the PDF file.
     * @param array $details Data to pass to the view.
     */
    public function __construct($pathToPdf, $details)
    {
        $this->pathToPdf = $pathToPdf;
        $this->details = $details;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.approved')
                    ->subject('Pemberitahuan Persetujuan Berkas')
                    ->with([
                        'details' => $this->details
                    ])
                    ->attach($this->pathToPdf, [
                        'as' => 'filename.pdf',
                        'mime' => 'application/pdf',
                    ]);
    }
    
}
