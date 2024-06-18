<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendPdfEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $pdfData;
    public $fileName;
    public $details;

    /**
     * Create a new message instance.
     *
     * @param string $pdfData Binary data of the PDF file.
     * @param string $fileName The name of the PDF file.
     * @param array $details Data to pass to the view.
     */
    public function __construct($pdfData, $fileName, array $details)
    {
        $this->pdfData = $pdfData;
        $this->fileName = $fileName;
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
                    ->with(['details' => $this->details])
                    ->attachData($this->pdfData, $this->fileName, [
                        'mime' => 'application/pdf',
                    ]);
    }
}
