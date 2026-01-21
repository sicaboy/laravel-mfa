<?php

namespace Sicaboy\LaravelMFA\Test\Unit;

use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;
use Mockery;
use Sicaboy\LaravelMFA\Helpers\MFAHelper;
use Sicaboy\LaravelMFA\Http\Middleware\MFA;
use Sicaboy\LaravelMFA\Test\TestCase;
use Symfony\Component\HttpFoundation\Response;

class MFAMiddlewareTest extends TestCase
{
    private $middleware;
    private $helper;
    private $generator;
    private $mockUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->helper = Mockery::mock(MFAHelper::class);
        $this->generator = $this->app['url'];
        $this->middleware = new MFA($this->generator, $this->helper);
        
        $this->mockUser = Mockery::mock();
        $this->mockUser->id = 1;
    }

    public function testMiddlewarePassesThroughWhenMFADisabled()
    {
        $request = Request::create('/test');
        $next = function ($req) {
            return response('success');
        };

        $this->helper->shouldReceive('getConfigByGroup')
            ->with('enabled', 'default')
            ->andReturn(false);

        $response = $this->middleware->handle($request, $next);

        $this->assertEquals('success', $response->getContent());
    }

    public function testMiddlewareRedirectsToLoginWhenNotAuthenticated()
    {
        $request = Request::create('/test');
        $next = function ($req) {
            return response('success');
        };

        $this->helper->shouldReceive('getConfigByGroup')
            ->with('enabled', 'default')
            ->andReturn(true);
        
        $this->helper->shouldReceive('getUserModel')
            ->with('default')
            ->andReturn(null);
        
        $this->helper->shouldReceive('getConfigByGroup')
            ->with('login_route', 'default', 'login')
            ->andReturn('login');

        $response = $this->middleware->handle($request, $next);

        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testMiddlewareReturnsJsonWhenNotAuthenticatedAndRequestWantsJson()
    {
        $request = Request::create('/test');
        $request->headers->set('Accept', 'application/json');
        $next = function ($req) {
            return response('success');
        };

        $this->helper->shouldReceive('getConfigByGroup')
            ->with('enabled', 'default')
            ->andReturn(true);
        
        $this->helper->shouldReceive('getUserModel')
            ->with('default')
            ->andReturn(null);
        
        $this->helper->shouldReceive('getConfigByGroup')
            ->with('login_route', 'default', 'login')
            ->andReturn('login');

        $response = $this->middleware->handle($request, $next);

        $this->assertEquals(403, $response->getStatusCode());
        $this->assertStringContainsString('Login required', $response->getContent());
    }

    public function testMiddlewareRedirectsToMFAWhenNotVerified()
    {
        $request = Request::create('/test');
        $next = function ($req) {
            return response('success');
        };

        $this->helper->shouldReceive('getConfigByGroup')
            ->with('enabled', 'default')
            ->andReturn(true);
        
        $this->helper->shouldReceive('getUserModel')
            ->with('default')
            ->andReturn($this->mockUser);
        
        $this->helper->shouldReceive('isVerificationCompleted')
            ->with('default')
            ->andReturn(false);

        $response = $this->middleware->handle($request, $next);

        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testMiddlewareReturnsJsonWhenNotVerifiedAndRequestWantsJson()
    {
        $request = Request::create('/test');
        $request->headers->set('Accept', 'application/json');
        $next = function ($req) {
            return response('success');
        };

        $this->helper->shouldReceive('getConfigByGroup')
            ->with('enabled', 'default')
            ->andReturn(true);
        
        $this->helper->shouldReceive('getUserModel')
            ->with('default')
            ->andReturn($this->mockUser);
        
        $this->helper->shouldReceive('isVerificationCompleted')
            ->with('default')
            ->andReturn(false);

        $response = $this->middleware->handle($request, $next);

        $this->assertEquals(423, $response->getStatusCode());
        $this->assertStringContainsString('MFA Required', $response->getContent());
    }

    public function testMiddlewareAllowsAccessWhenVerified()
    {
        $request = Request::create('/test');
        $next = function ($req) {
            return response('success');
        };

        $this->helper->shouldReceive('getConfigByGroup')
            ->with('enabled', 'default')
            ->andReturn(true);
        
        $this->helper->shouldReceive('getUserModel')
            ->with('default')
            ->andReturn($this->mockUser);
        
        $this->helper->shouldReceive('isVerificationCompleted')
            ->with('default')
            ->andReturn(true);

        $response = $this->middleware->handle($request, $next);

        $this->assertEquals('success', $response->getContent());
    }

    public function testMiddlewareWorksWithCustomGroup()
    {
        $request = Request::create('/test');
        $next = function ($req) {
            return response('success');
        };

        $this->helper->shouldReceive('getConfigByGroup')
            ->with('enabled', 'admin')
            ->andReturn(true);
        
        $this->helper->shouldReceive('getUserModel')
            ->with('admin')
            ->andReturn($this->mockUser);
        
        $this->helper->shouldReceive('isVerificationCompleted')
            ->with('admin')
            ->andReturn(true);

        $response = $this->middleware->handle($request, $next, 'admin');

        $this->assertEquals('success', $response->getContent());
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
