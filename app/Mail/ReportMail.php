<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public array $reportData,
        public string $pdfContent,
        public string $filename
    ) {
    }

    public function build(): self
    {
        return $this->subject('Laporan Apotek')
            ->view('emails.report')
            ->with([
                'filters' => $this->reportData['filters'] ?? [],
                'meta' => $this->reportData['meta'] ?? [],
            ])
            ->attachData($this->pdfContent, $this->filename, [
                'mime' => 'application/pdf',
            ]);
    }
}
