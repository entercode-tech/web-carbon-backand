<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $view;
    public array $content;
    public $attachmentFile;

    /**
     * Create a new message instance.
     */
    public function __construct($subject, $view, $content, $attachmentFile = [])
    {
        $this->subject = $subject;
        $this->view = $view;
        $this->content = $content;
        $this->attachmentFile = $attachmentFile;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: $this->view,
            with: $this->content,
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachmentData = [];
        foreach ($this->attachmentFile as $attachment) {
            $attachmentData[] = Attachment::fromStorage($attachment);
        }

        return $attachmentData;
    }
}
