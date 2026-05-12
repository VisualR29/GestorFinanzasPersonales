<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserRoleUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_update_user_role(): void
    {
        $user = User::factory()->create();

        $response = $this->patch(route('admin.usuarios.update-role', $user), [
            'role' => User::ROLE_ADMIN,
        ]);

        $response->assertRedirect(route('login', absolute: false));
    }

    public function test_regular_user_gets_redirect_when_updating_role(): void
    {
        $acting = User::factory()->create(['role' => User::ROLE_USUARIO]);
        $target = User::factory()->create(['role' => User::ROLE_USUARIO]);

        $response = $this->actingAs($acting)->patch(route('admin.usuarios.update-role', $target), [
            'role' => User::ROLE_ADMIN,
        ]);

        $response->assertRedirect(route('dashboard', absolute: false));
        $response->assertSessionHas('error');
    }

    public function test_admin_can_promote_user_to_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->create(['role' => User::ROLE_USUARIO]);

        $response = $this->actingAs($admin)->from(route('admin.usuarios.index'))
            ->patch(route('admin.usuarios.update-role', $target), [
                'role' => User::ROLE_ADMIN,
            ]);

        $response->assertRedirect(route('admin.usuarios.index', absolute: false));
        $response->assertSessionHas('status');
        $this->assertTrue($target->fresh()->isAdmin());
    }

    public function test_admin_cannot_remove_last_admin(): void
    {
        $soleAdmin = User::factory()->admin()->create();

        $response = $this->actingAs($soleAdmin)->from(route('admin.usuarios.index'))
            ->patch(route('admin.usuarios.update-role', $soleAdmin), [
                'role' => User::ROLE_USUARIO,
            ]);

        $response->assertRedirect(route('admin.usuarios.index', absolute: false));
        $response->assertSessionHasErrors('role');
        $this->assertTrue($soleAdmin->fresh()->isAdmin());
    }
}
