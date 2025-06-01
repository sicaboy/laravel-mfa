<?php

namespace Sicaboy\LaravelMFA\Test\Unit;

use Mockery;
use Sicaboy\LaravelMFA\Helpers\MFAHelper;
use Sicaboy\LaravelMFA\Listeners\ClearMFAStatus;
use Sicaboy\LaravelMFA\Test\TestCase;

class ClearMFAStatusTest extends TestCase
{
    private $helper;
    private $listener;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->helper = Mockery::mock(MFAHelper::class);
        $this->listener = new ClearMFAStatus($this->helper);
    }

    public function testHandleClearsMFAStatusForAllGroups()
    {
        $mockUser = Mockery::mock();
        $mockUser->id = 1;
        
        $mockEvent = Mockery::mock();
        $mockEvent->user = $mockUser;
        
        // Set up groups configuration
        config()->set('laravel-mfa.group', [
            'admin' => [],
            'user' => [],
        ]);
        
        // Expect clearVerificationCompleted to be called for each group
        $this->helper->shouldReceive('clearVerificationCompleted')
            ->with('admin', 1)
            ->once();
            
        $this->helper->shouldReceive('clearVerificationCompleted')
            ->with('user', 1)
            ->once();
            
        $this->helper->shouldReceive('clearVerificationCompleted')
            ->with('default', 1)
            ->once();
        
        $this->listener->handle($mockEvent);
        
        // If we get here without exceptions, the test passes
        $this->assertTrue(true);
    }

    public function testHandleClearsMFAStatusForDefaultGroupOnly()
    {
        $mockUser = Mockery::mock();
        $mockUser->id = 1;
        
        $mockEvent = Mockery::mock();
        $mockEvent->user = $mockUser;
        
        // No groups configured
        config()->set('laravel-mfa.group', []);
        
        // Expect clearVerificationCompleted to be called only for default
        $this->helper->shouldReceive('clearVerificationCompleted')
            ->with('default', 1)
            ->once();
        
        $this->listener->handle($mockEvent);
        
        // If we get here without exceptions, the test passes
        $this->assertTrue(true);
    }

    public function testHandleDoesNothingWhenUserIdIsEmpty()
    {
        $mockUser = Mockery::mock();
        $mockUser->id = null;
        
        $mockEvent = Mockery::mock();
        $mockEvent->user = $mockUser;
        
        // Expect no calls to clearVerificationCompleted
        $this->helper->shouldNotReceive('clearVerificationCompleted');
        
        $this->listener->handle($mockEvent);
        
        // If we get here without exceptions, the test passes
        $this->assertTrue(true);
    }

    public function testHandleDoesNothingWhenUserIsNull()
    {
        $mockEvent = Mockery::mock();
        $mockEvent->user = null;
        
        // Expect no calls to clearVerificationCompleted
        $this->helper->shouldNotReceive('clearVerificationCompleted');
        
        $this->listener->handle($mockEvent);
        
        // If we get here without exceptions, the test passes
        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
