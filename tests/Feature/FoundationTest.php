<?php

namespace Tests\Feature;

use App\Models\BalanceAccount;
use App\Models\Location;
use App\Models\PaymentMethod;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class FoundationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_superadmin_user_can_login_with_username_and_password(): void
    {
        $credentials = [
            'username' => 'superadmin',
            'password' => 'password',
        ];

        $this->assertTrue(Auth::attempt($credentials));

        $user = Auth::user();
        $this->assertNotNull($user);
        $this->assertEquals('superadmin', $user->username);
        $this->assertTrue($user->hasRole('OWNER'));
    }

    public function test_role_and_permission_matrix(): void
    {
        $owner = User::where('username', 'superadmin')->first();
        $this->assertTrue($owner->hasPermission('report.profit.view'));
        $this->assertTrue($owner->hasPermission('user.manage'));

        // Create a cashier user
        $cashierRole = Role::where('name', 'CASHIER')->first();
        $cashier = User::create([
            'name' => 'Cashier Test',
            'username' => 'cashier1',
            'password' => bcrypt('password'),
            'role_id' => $cashierRole->id,
            'status' => 'ACTIVE',
        ]);

        $this->assertTrue($cashier->hasRole('CASHIER'));
        $this->assertTrue($cashier->hasPermission('sales.create'));
        $this->assertFalse($cashier->hasPermission('report.profit.view'));
        $this->assertFalse($cashier->hasPermission('cost_price.view'));
        $this->assertFalse($cashier->hasPermission('user.manage'));
    }

    public function test_seed_is_idempotent(): void
    {
        // Re-run seeder a second time
        $this->seed(DatabaseSeeder::class);

        $this->assertEquals(3, Role::count()); // OWNER, ADMIN, CASHIER
        $this->assertEquals(1, User::where('username', 'superadmin')->count());
        $this->assertEquals(4, PaymentMethod::count()); // CASH, QRIS, TRANSFER, E_WALLET
        $this->assertEquals(7, BalanceAccount::count()); // CASH, QRIS, BANK BCA, BANK MAS, DANA, MULTI, WAHANA
        $this->assertEquals(1, Location::where('code', 'RAJA-BANGO')->count());
        $this->assertEquals('Raja Aksesoris', Setting::get('store_name'));
    }

    public function test_custom_admin_panel_renders_and_allows_login(): void
    {
        $superadmin = User::where('username', 'superadmin')->first();
        $this->actingAs($superadmin);

        $adminResponse = $this->get('/admin');
        $adminResponse->assertStatus(200);
        $adminResponse->assertSee('Dashboard Overview');
    }
}
