<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $email;
    public $senderName;
    public $senderMail;
    public $receiverName;
    public $receiverMail;
    public $messageContent;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email, $senderName, $senderMail, $receiverName, $receiverMail, $messageContent)
    {
        $this->email = $email;
        $this->senderName = $senderName;
        $this->senderMail = $senderMail;
        $this->receiverName = $receiverName;
        $this->receiverMail = $receiverMail;
        $this->messageContent = $messageContent;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        //"\u{1F913}"
        return $this->subject('Tu as reçu un nouveau message !')
        ->markdown('mails.contactUser');
    }
}
