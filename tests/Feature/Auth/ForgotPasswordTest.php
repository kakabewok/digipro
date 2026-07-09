<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
{
    public function test_forgot_password_page_is_accessible()
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
    }

    public function test_reset_link_is_sent_to_valid_email()
    {
        $user = User::factory()->create();

        $response = $this->post('/forgot-password', [
            'email' => $user->email,
        ]);

        $response->assertSessionHas('status');
        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => $user->email,
        ]);
    }

    public function test_no_error_revealed_for_non_existent_email()
    {
        // Many systems return same success message to avoid email enumeration
        $response = $this->post('/forgot-password', [
            'email' => 'doesnotexist@example.com',
        ]);

        // Standard Laravel might return email error if not using "silent" approach.
        // The prompt says "no error revealed for non-existent email (security)", 
        // implying it should return success or at least not fail validation.
        $response->assertSessionDoesntHaveErrors(['email']);
        $response->assertSessionHas('status');
    }

    public function test_user_can_reset_password_with_valid_token()
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertRedirect('/login'); // or dashboard, usually login
        
        $user->refresh();
        $this->assertTrue(Hash::check('new-password', $user->password));
    }

    public function test_reset_fails_with_expired_token()
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);
        
        // Expire token by directly modifying db
        DB::table('password_reset_tokens')->where('email', $user->email)->update([
            'created_at' => now()->subHours(5)
        ]);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_reset_fails_with_mismatched_password_confirmation()
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'different-password',
        ]);

        $response->assertSessionHasErrors('password');
    }
}
