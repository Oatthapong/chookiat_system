<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    protected $admin;
    protected $normalUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::where('username', 'admin')->first();
        $this->normalUser = User::where('username', 'user1')->first();
    }

    /**
     * Test 1: Guest cannot access /users
     */
    public function test_guest_cannot_access_user_management(): void
    {
        $response = $this->get('/users');
        $response->assertRedirect('/login');
    }

    /**
     * Test 2: Normal user (role: user) cannot access /users (Forbidden 403)
     */
    public function test_normal_user_cannot_access_user_management(): void
    {
        $response = $this->actingAs($this->normalUser)->get('/users');
        $response->assertStatus(403);
    }

    /**
     * Test 3: Admin can access /users and view KPI summary
     */
    public function test_admin_can_access_user_management(): void
    {
        $response = $this->actingAs($this->admin)->get('/users');
        $response->assertStatus(200);
        $response->assertSee('ระบบจัดการผู้ใช้งาน');
        $response->assertSee('user1');
    }

    /**
     * Test 4: Admin can update/reset user data (Name, Username, Email)
     */
    public function test_admin_can_reset_user_data(): void
    {
        $response = $this->actingAs($this->admin)->putJson("/users/{$this->normalUser->id}", [
            'name' => 'General User Updated',
            'username' => 'user1',
            'email' => 'user1@chookiat.com',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $this->normalUser->id,
            'name' => 'General User Updated',
        ]);

        // Revert name back
        $this->normalUser->update(['name' => 'General User']);
    }

    /**
     * Test 5: Admin can reset user's password
     */
    public function test_admin_can_reset_user_password(): void
    {
        $response = $this->actingAs($this->admin)->postJson("/users/{$this->normalUser->id}/reset-password", [
            'password' => 'newsecret123',
            'password_confirmation' => 'newsecret123',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        // Check that new password works
        $userFresh = User::find($this->normalUser->id);
        $this->assertTrue(Hash::check('newsecret123', $userFresh->password));

        // Reset back to original password123
        $userFresh->password = Hash::make('password123');
        $userFresh->save();
    }

    /**
     * Test 6: Admin can disable user login (is_active = 0) and re-enable it
     */
    public function test_admin_can_toggle_user_active_status(): void
    {
        // Ensure starting state is active (true)
        $this->normalUser->update(['is_active' => true]);

        // Toggle to inactive (0)
        $response = $this->actingAs($this->admin)->patchJson("/users/{$this->normalUser->id}/toggle-status");
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'is_active' => false,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $this->normalUser->id,
            'is_active' => false,
        ]);

        // Logout admin session before trying to log in as user1
        auth()->logout();

        // Attempt login as deactivated user -> should fail
        $loginResponse = $this->postJson('/login', [
            'username' => 'user1',
            'password' => 'password123',
        ]);
        $loginResponse->assertStatus(403);
        $loginResponse->assertJson([
            'success' => false,
            'message' => 'บัญชีผู้ใช้นี้ถูกระงับการใช้งาน กรุณาติดต่อผู้ดูแลระบบ',
        ]);

        // Toggle back to active (1)
        $reEnableResponse = $this->actingAs($this->admin)->patchJson("/users/{$this->normalUser->id}/toggle-status");
        $reEnableResponse->assertStatus(200);
        $reEnableResponse->assertJson([
            'success' => true,
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $this->normalUser->id,
            'is_active' => true,
        ]);
    }

    /**
     * Test 7: Admin cannot disable own account
     */
    public function test_admin_cannot_disable_own_account(): void
    {
        $response = $this->actingAs($this->admin)->patchJson("/users/{$this->admin->id}/toggle-status");
        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'ไม่สามารถยกเลิกการใช้งานบัญชีที่กำลังเข้าสู่ระบบอยู่ได้',
        ]);
    }
}
