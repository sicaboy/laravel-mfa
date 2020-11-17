<?php

namespace Sicaboy\LaravelMFA\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Sicaboy\LaravelMFA\Helpers\MFAHelper;

/**
 * Class SendMFAMail
 * @package Sicaboy\LaravelMFA\Mail
 */
class SendMFAMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * @var string
     */
    protected $emailTemplate;

    /**
     * @var array
     */
    protected $emailVars;

    /**
     * @var string
     */
    protected $emailSubject;

    public function __construct(string $emailTemplate, array $emailVars, string $emailSubject)
    {
        $this->emailTemplate = $emailTemplate;
        $this->emailVars = $emailVars;
        $this->emailSubject = $emailSubject;
    }
    
    public function build()
    {
        return $this->view($this->emailTemplate)
                    ->with($this->emailVars)
                    ->subject($this->emailSubject);
    }
}
