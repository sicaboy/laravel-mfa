<?php

namespace Sicaboy\LaravelMFA\Test\Unit;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Mockery;
use Sicaboy\LaravelMFA\Helpers\MFAHelper;
use Sicaboy\LaravelMFA\Http\Controllers\MFAController;
use Sicaboy\LaravelMFA\Mail\SendMFAMail;
use Sicaboy\LaravelMFA\Test\TestCase;

class MFAControllerTest extends TestCase
{
    private $controller;
    private $helper;
    private $mockUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->helper = Mockery::mock(MFAHelper::class);
        $this->mockUser = Mockery::mock();
        $this->mockUser->id = 1;
        $this->mockUser->email = 'test@example.com';
        
        Mail::fake();
    }

    public function testGetFormDisplaysFormWhenUserAuthenticated()
    {
        $request = Request::create('/mfa/form', 'GET', ['group' => 'default']);
        $controller = new MFAController($this->helper, $request);
        
        $this->helper->shouldReceive('getUserModel')
            ->with('default')
            ->andReturn($this->mockUser);
        
        $this->helper->shouldReceive('isVerificationCompleted')
            ->with('default')
            ->andReturn(false);
        
        $this->helper->shouldReceive('getConfigByGroup')
            ->with('code_expire_after_minutes', 'default', 10)
            ->andReturn(10);

        $response = $controller->getForm($request);
        
        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
    }

    public function testGetFormRedirectsWhenUserNotAuthenticated()
    {
        $request = Request::create('/mfa/form', 'GET', ['group' => 'default']);
        $controller = new MFAController($this->helper, $request);
        
        $this->helper->shouldReceive('getUserModel')
            ->with('default')
            ->andReturn(null);
        
        $this->helper->shouldReceive('getConfigByGroup')
            ->with('login_route', 'default', 'login')
            ->andReturn('login');

        $response = $controller->getForm($request);
        
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testPostFormSucceedsWithCorrectCode()
    {
        $request = Request::create('/mfa/form', 'POST', [
            'group' => 'default',
            'code' => '123456'
        ]);
        $controller = new MFAController($this->helper, $request);
        
        $this->helper->shouldReceive('getUserModel')
            ->with('default')
            ->andReturn($this->mockUser);
        
        $this->helper->shouldReceive('isVerificationCompleted')
            ->with('default')
            ->andReturn(false);
        
        // Mock cache get for stored code
        Cache::shouldReceive('get')
            ->with('mfa_code-default-1')
            ->andReturn('123456');
        
        $this->helper->shouldReceive('setVerificationCompleted')
            ->with('default')
            ->once();
        
        $this->helper->shouldReceive('getConfigByGroup')
            ->with('verified_route', 'default')
            ->andReturn(null);

        $response = $controller->postForm($request);
        
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testPostFormFailsWithIncorrectCode()
    {
        $request = Request::create('/mfa/form', 'POST', [
            'group' => 'default',
            'code' => '123456'
        ]);
        $controller = new MFAController($this->helper, $request);
        
        $this->helper->shouldReceive('getUserModel')
            ->with('default')
            ->andReturn($this->mockUser);
        
        $this->helper->shouldReceive('isVerificationCompleted')
            ->with('default')
            ->andReturn(false);
        
        // Mock cache get for stored code (different from input)
        Cache::shouldReceive('get')
            ->with('mfa_code-default-1')
            ->andReturn('654321');

        $response = $controller->postForm($request);
        
        $this->assertEquals(302, $response->getStatusCode());
        // Should redirect back with error
    }

    public function testPostFormFailsWithExpiredCode()
    {
        $request = Request::create('/mfa/form', 'POST', [
            'group' => 'default',
            'code' => '123456'
        ]);
        $controller = new MFAController($this->helper, $request);
        
        $this->helper->shouldReceive('getUserModel')
            ->with('default')
            ->andReturn($this->mockUser);
        
        $this->helper->shouldReceive('isVerificationCompleted')
            ->with('default')
            ->andReturn(false);
        
        // Mock cache get returns null (expired)
        Cache::shouldReceive('get')
            ->with('mfa_code-default-1')
            ->andReturn(null);

        $response = $controller->postForm($request);
        
        $this->assertEquals(302, $response->getStatusCode());
        // Should redirect back with error
    }

    public function testPostFormRedirectsWhenUserNotAuthenticated()
    {
        $request = Request::create('/mfa/form', 'POST', [
            'group' => 'default',
            'code' => '123456'
        ]);
        $controller = new MFAController($this->helper, $request);
        
        $this->helper->shouldReceive('getUserModel')
            ->with('default')
            ->andReturn(null);
        
        $this->helper->shouldReceive('getConfigByGroup')
            ->with('login_route', 'default', 'login')
            ->andReturn('login');

        $response = $controller->postForm($request);
        
        $this->assertEquals(302, $response->getStatusCode());
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
