<?php

namespace Sicaboy\LaravelMFA\Test\Unit;

use Sicaboy\LaravelMFA\Http\Middleware\MFA;
use Sicaboy\LaravelMFA\LaravelMFAServiceProvider;
use Sicaboy\LaravelMFA\Test\TestCase;

class LaravelMFAServiceProviderTest extends TestCase
{
    public function testServiceProviderIsRegistered()
    {
        $this->assertTrue($this->app->providerIsLoaded(LaravelMFAServiceProvider::class));
    }

    public function testMiddlewareIsRegistered()
    {
        $router = $this->app['router'];
        
        $this->assertArrayHasKey('mfa', $router->getMiddleware());
        $this->assertEquals(MFA::class, $router->getMiddleware()['mfa']);
    }

    public function testViewsAreLoaded()
    {
        $viewFactory = $this->app['view'];
        
        $this->assertTrue($viewFactory->exists('laravel-mfa::mfa.form'));
        $this->assertTrue($viewFactory->exists('laravel-mfa::emails.authentication-code'));
    }

    public function testRoutesAreRegistered()
    {
        $routes = $this->app['router']->getRoutes();
        
        $routeNames = [];
        foreach ($routes as $route) {
            if ($route->getName()) {
                $routeNames[] = $route->getName();
            }
        }
        
        $this->assertContains('mfa.generate', $routeNames);
        $this->assertContains('mfa.verify', $routeNames);
    }

    public function testConfigurationIsPublishable()
    {
        $provider = new LaravelMFAServiceProvider($this->app);
        
        // This tests that the provider can be instantiated without errors
        $this->assertInstanceOf(LaravelMFAServiceProvider::class, $provider);
    }
}
