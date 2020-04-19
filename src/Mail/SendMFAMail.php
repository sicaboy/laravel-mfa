<?php

namespace Sicaboy\LaravelMFA\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Sicaboy\LaravelMFA\Helpers\MFAHelper;

class SendMFAMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $emailTemplate;
    protected $emailVars;
    protected $emailSubject;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($emailTemplate, $emailVars, $emailSubject)
    {
        $this->emailTemplate = $emailTemplate;
        $this->emailVars = $emailVars;
        $this->emailSubject = $emailSubject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view($this->emailTemplate)
                    ->with($this->emailVars)
                    ->subject($this->emailSubject);
    }
}
