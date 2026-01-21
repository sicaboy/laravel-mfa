<?php

namespace Sicaboy\LaravelMFA\Test\Unit;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Sicaboy\LaravelMFA\Helpers\MFAHelper;
use Sicaboy\LaravelMFA\Test\TestCase;

class MFAHelperTest extends TestCase
{
    private $helper;
    private $mockUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->helper = new MFAHelper();
        
        $this->mockUser = Mockery::mock();
        $this->mockUser->id = 1;
        $this->mockUser->email = 'test@example.com';
    }

    public function testGetConfigByGroupReturnsGroupConfig()
    {
        config()->set('laravel-mfa.group.admin.login_route', 'admin.login');
        
        $result = $this->helper->getConfigByGroup('login_route', 'admin');
        
        $this->assertEquals('admin.login', $result);
    }

    public function testGetConfigByGroupFallsBackToDefault()
    {
        config()->set('laravel-mfa.default.login_route', 'login');
        
        $result = $this->helper->getConfigByGroup('login_route', 'nonexistent');
        
        $this->assertEquals('login', $result);
    }

    public function testGetConfigByGroupReturnsDefaultValue()
    {
        $result = $this->helper->getConfigByGroup('nonexistent_key', 'nonexistent', 'default_value');
        
        $this->assertEquals('default_value', $result);
    }

    public function testIsVerificationCompletedReturnsFalseWhenNotCompleted()
    {
        Auth::shouldReceive('user')->andReturn($this->mockUser);
        
        $result = $this->helper->isVerificationCompleted('default');
        
        $this->assertFalse($result);
    }

    public function testIsVerificationCompletedReturnsTrueWhenCompleted()
    {
        Auth::shouldReceive('user')->andReturn($this->mockUser);
        Cache::shouldReceive('has')
            ->with('mfa_completed_default_1')
            ->andReturn(true);
        
        $result = $this->helper->isVerificationCompleted('default');
        
        $this->assertTrue($result);
    }

    public function testSetVerificationCompleted()
    {
        Auth::shouldReceive('user')->andReturn($this->mockUser);
        Cache::shouldReceive('put')
            ->with('mfa_completed_default_1', true, Mockery::any())
            ->once();
        
        $this->helper->setVerificationCompleted('default');
        
        // If we get here without exception, the test passes
        $this->assertTrue(true);
    }

    public function testClearVerificationCompleted()
    {
        Cache::shouldReceive('forget')
            ->with('mfa_completed_default_1')
            ->once();
        
        $this->helper->clearVerificationCompleted('default', 1);
        
        // If we get here without exception, the test passes
        $this->assertTrue(true);
    }

    public function testRefreshVerificationCodeGeneratesAndStoresCode()
    {
        $cacheKey = 'mfa_code_1';
        $expiryMinutes = 10;
        
        Cache::shouldReceive('put')
            ->with($cacheKey, Mockery::type('int'), Mockery::any())
            ->once();
        
        $code = $this->helper->refreshVerificationCode($cacheKey, $expiryMinutes);
        
        $this->assertIsInt($code);
        $this->assertGreaterThanOrEqual(100000, $code);
        $this->assertLessThanOrEqual(999999, $code);
    }

    public function testGetUserModelUsesCustomClosure()
    {
        $customUser = Mockery::mock();
        $customUser->id = 2;
        
        config()->set('laravel-mfa.group.admin.auth_user_closure', function () use ($customUser) {
            return $customUser;
        });
        
        $result = $this->helper->getUserModel('admin');
        
        $this->assertEquals($customUser, $result);
    }

    public function testGetUserModelUsesDefaultAuthWhenNoCustomClosure()
    {
        Auth::shouldReceive('user')->andReturn($this->mockUser);
        
        $result = $this->helper->getUserModel('default');
        
        $this->assertEquals($this->mockUser, $result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
