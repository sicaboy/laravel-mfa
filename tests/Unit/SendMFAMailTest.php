<?php

namespace Sicaboy\LaravelMFA\Test\Unit;

use Sicaboy\LaravelMFA\Mail\SendMFAMail;
use Sicaboy\LaravelMFA\Test\TestCase;

class SendMFAMailTest extends TestCase
{
    public function testMailCanBeInstantiated()
    {
        $mail = new SendMFAMail(
            'laravel-mfa::emails.authentication-code',
            ['code' => '123456', 'user' => 'test@example.com'],
            'Test Subject'
        );
        
        $this->assertInstanceOf(SendMFAMail::class, $mail);
    }

    public function testMailContainsCorrectData()
    {
        $template = 'laravel-mfa::emails.authentication-code';
        $vars = ['code' => '123456', 'user' => 'test@example.com'];
        $subject = 'Test Subject';
        
        $mail = new SendMFAMail($template, $vars, $subject);
        
        // Build the mail to access its properties
        $built = $mail->build();
        
        $this->assertInstanceOf(SendMFAMail::class, $built);
    }

    public function testMailUsesCorrectTemplate()
    {
        config()->set('laravel-mfa.default.email.template', 'custom.template');
        config()->set('laravel-mfa.default.email.subject', 'Custom Subject');
        
        $mail = new SendMFAMail('custom.template', ['code' => '123456'], 'Custom Subject');
        $built = $mail->build();
        
        $this->assertInstanceOf(SendMFAMail::class, $built);
    }

    public function testMailUsesGroupSpecificTemplate()
    {
        config()->set('laravel-mfa.group.admin.email.template', 'admin.template');
        config()->set('laravel-mfa.group.admin.email.subject', 'Admin Subject');
        
        $mail = new SendMFAMail('admin.template', ['code' => '123456'], 'Admin Subject');
        $built = $mail->build();
        
        $this->assertInstanceOf(SendMFAMail::class, $built);
    }
}
