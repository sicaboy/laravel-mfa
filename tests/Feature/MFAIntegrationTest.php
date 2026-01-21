<?php

namespace Sicaboy\LaravelMFA\Test\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Sicaboy\LaravelMFA\Test\TestCase;
use Sicaboy\LaravelMFA\Test\Models\User;

class MFAIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Set up test routes
        Route::middleware(['web', 'mfa'])->get('/protected', function () {
            return 'Protected Content';
        });
        
        Route::middleware(['web', 'mfa:admin'])->get('/admin/protected', function () {
            return 'Admin Protected Content';
        });
        
        Route::get('/login', function () {
            return 'Login Page';
        })->name('login');
        
        Mail::fake();
    }

    public function testUnauthenticatedUserIsRedirectedToLogin()
    {
        $response = $this->get('/protected');
        
        $response->assertRedirect('/login');
    }

    public function testAuthenticatedUserWithoutMFAIsRedirectedToMFAForm()
    {
        $user = $this->createUser();
        $this->actingAs($user);
        
        $response = $this->get('/protected');
        
        $response->assertRedirect();
        $this->assertStringContainsString('mfa/generate', $response->headers->get('Location'));
    }

    public function testUserCanAccessMFAGenerationForm()
    {
        $user = $this->createUser();
        $this->actingAs($user);
        
        // Ensure user is not verified yet
        Cache::forget('mfa_completed_default_' . $user->id);
        
        $response = $this->get('/mfa/generate?group=default');
        
        $response->assertStatus(302);
        $response->assertRedirect('/mfa/form?group=default');
    }

    public function testUserCanVerifyWithCorrectCode()
    {
        $user = $this->createUser();
        $this->actingAs($user);
        
        // Store a code in cache
        $code = '123456';
        Cache::put('mfa_code-default-' . $user->id, $code, now()->addMinutes(10));
        
        $response = $this->post('/mfa/verify', [
            'group' => 'default',
            'code' => $code,
        ]);
        
        $response->assertRedirect('/');
        
        // Now accessing protected route should work
        $response = $this->get('/protected');
        $response->assertStatus(200);
        $response->assertSeeText('Protected Content');
    }

    public function testUserCannotVerifyWithIncorrectCode()
    {
        $user = $this->createUser();
        $this->actingAs($user);
        
        // Store a code in cache
        Cache::put('mfa_code-default-' . $user->id, '123456', now()->addMinutes(10));
        
        $response = $this->post('/mfa/verify', [
            'group' => 'default',
            'code' => '654321', // Wrong code
        ]);
        
        $response->assertRedirect();
        $response->assertSessionHasErrors();
    }

    public function testUserCannotVerifyWithExpiredCode()
    {
        $user = $this->createUser();
        $this->actingAs($user);
        
        // Don't store any code (simulating expiration)
        
        $response = $this->post('/mfa/verify', [
            'group' => 'default',
            'code' => '123456',
        ]);
        
        $response->assertRedirect();
        $response->assertSessionHasErrors();
    }

    public function testMFAWorksWithCustomGroups()
    {
        config()->set('laravel-mfa.group.admin', [
            'login_route' => 'login',
            'auth_user_closure' => function () {
                return auth()->user();
            },
        ]);
        
        $user = $this->createUser();
        $this->actingAs($user);
        
        // First request should redirect to MFA
        $response = $this->get('/admin/protected');
        $response->assertRedirect();
        
        // Verify with admin group
        $code = '123456';
        Cache::put('mfa_code-admin-' . $user->id, $code, now()->addMinutes(10));
        
        $response = $this->post('/mfa/verify', [
            'group' => 'admin',
            'code' => $code,
        ]);
        
        $response->assertRedirect('/');
        
        // Now admin route should be accessible
        $response = $this->get('/admin/protected');
        $response->assertStatus(200);
        $response->assertSeeText('Admin Protected Content');
    }

    public function testMFACanBeDisabled()
    {
        config()->set('laravel-mfa.default.enabled', false);
        
        $user = $this->createUser();
        $this->actingAs($user);
        
        $response = $this->get('/protected');
        
        $response->assertStatus(200);
        $response->assertSeeText('Protected Content');
    }

    public function testApiRequestsReceiveJsonResponses()
    {
        // Test unauthenticated API request
        $response = $this->getJson('/protected');
        
        $response->assertStatus(403);
        $response->assertJson(['error' => 'Login required']);
        
        // Test authenticated but unverified API request
        $user = $this->createUser();
        $this->actingAs($user);
        
        $response = $this->getJson('/protected');
        
        $response->assertStatus(423);
        $response->assertJson(['error' => 'MFA Required']);
    }

    private function createUser()
    {
        $user = new User([
            'id' => 1,
            'email' => 'test@example.com',
            'name' => 'Test User',
        ]);
        $user->exists = true; // Mark as existing so it behaves like a saved model
        return $user;
    }
}
