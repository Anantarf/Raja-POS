<?php

namespace Tests\Feature;

use App\Models\BalanceAccount;
use App\Models\Location;
use App\Models\PaymentMethod;
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

    public function test_admin_management_menu_routes_render_for_owner(): void
    {
        $superadmin = User::where('username', 'superadmin')->first();

        $this->actingAs($superadmin)
            ->get('/admin/dashboard')
            ->assertStatus(200)
            ->assertSee('Operasional Kasir')
            ->assertSee('Katalog &amp; Stok Barang', false)
            ->assertSee('Keuangan &amp; Saldo', false)
            ->assertSee('Laporan Toko')
            ->assertSee('Pengaturan Owner');

        $this->get('/admin/inventory-movements')
            ->assertStatus(200)
            ->assertSee('Riwayat Stok Masuk / Keluar');

        $this->get('/admin/reports/sales')
            ->assertStatus(200)
            ->assertSee('Laporan')
            ->assertSee('Penjualan');

        $this->get('/admin/settings/payment-methods')
            ->assertStatus(200)
            ->assertSee('Metode Pembayaran');
    }

    public function test_admin_panel_and_custom_dashboard_render_for_authenticated_user(): void
    {
        $superadmin = User::where('username', 'superadmin')->first();
        $this->actingAs($superadmin);

        $panelResponse = $this->get('/admin');
        $panelResponse->assertStatus(200);
        $panelResponse->assertSee('Dashboard');

        $dashboardResponse = $this->get('/admin/dashboard');
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Ringkasan Operasional');
    }
}
