<?php

declare(strict_types=1);

namespace Tests\Feature\V1\Auth;

use App\Domains\System\Users\SystemUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered()
    {
        $response = $this->get('/v1/login');
        $response->assertStatus(200);
    }

    public function test_system_login_screen_can_be_rendered()
    {
        $response = $this->get('/v1/system-login');
        $response->assertStatus(200);
    }

    public function test_org_users_can_authenticate_using_the_login_screen()
    {
        $orgUser = $this->generateRandomOrganization()['organization_user'];

        $this->post('/v1/login', [
            'email' => $orgUser->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated('web');
    }

    public function test_system_users_can_authenticate_using_the_login_screen(): void
    {
        $user = SystemUser::factory()->create();

        $this->post('/v1/system-login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated('system');
        //        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_users_can_not_authenticate_with_invalid_password()
    {
        $user = SystemUser::factory()->create();

        $this->post('/system-login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
    }

    //    public function test_users_can_logout()
    //    {
    //        $user = User::factory()->create();
    //
    //        $response = $this->actingAs($user)->post('/logout');
    //
    //        $this->assertGuest();
    //        $response->assertRedirect('/');
    //    }
}
