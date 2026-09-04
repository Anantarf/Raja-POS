<?php

namespace Database\Seeders;

use App\Models\BalanceAccount;
use App\Models\Location;
use App\Models\PaymentMethod;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Roles
        $ownerRole = Role::firstOrCreate(['name' => 'OWNER']);
        $adminRole = Role::firstOrCreate(['name' => 'ADMIN']);
        $cashierRole = Role::firstOrCreate(['name' => 'CASHIER']);

        // 2. Permissions
        $permissions = [
            'dashboard.view' => 'Access dashboard',
            'product.view' => 'View products',
            'product.create' => 'Create product',
            'product.update' => 'Update product',
            'product.delete' => 'Delete product',
            'cost_price.view' => 'View cost price / COGS',
            'inventory.view' => 'View inventory stock',
            'inventory.adjust' => 'Adjust stock manually',
            'stock_opname.view' => 'View stock opname',
            'stock_opname.create' => 'Create stock opname session',
            'stock_opname.approve' => 'Approve stock opname',
            'sales.create' => 'Create POS checkout sale',
            'sales.view_all' => 'View all sales history',
            'sales.view_own' => 'View own sales history',
            'sales.trash' => 'Move sale to Sampah Transaksi',
            'sales.restore' => 'Restore sale from Sampah Transaksi',
            'payment.view' => 'View payments',
            'balance.view' => 'View balance accounts',
            'balance.adjust' => 'Adjust balance transactions',
            'report.sales.view' => 'View sales report',
            'report.profit.view' => 'View profit report',
            'report.inventory.view' => 'View inventory report',
            'report.payment.view' => 'View payment report',
            'report.balance.view' => 'View balance report',
            'user.manage' => 'Manage system users',
            'role_permission.manage' => 'Manage roles & permissions',
            'audit_log.view' => 'View audit logs',
            'settings.manage' => 'Manage store settings',
            'excel_import.preview' => 'Preview Excel data import',
            'excel_import.commit' => 'Commit Excel data import',
        ];

        $permissionModels = [];
        foreach ($permissions as $name => $description) {
            $permissionModels[$name] = Permission::firstOrCreate(
                ['name' => $name],
                ['description' => $description]
            );
        }

        // Permission Assignment according to PRD Bab 49
        $adminPermissionKeys = [
            'dashboard.view', 'product.view', 'product.create', 'product.update',
            'cost_price.view', 'inventory.view', 'inventory.adjust',
            'stock_opname.view', 'stock_opname.create', 'stock_opname.approve',
            'sales.create', 'sales.view_all', 'sales.view_own', 'sales.trash', 'sales.restore',
            'payment.view', 'balance.view', 'balance.adjust',
            'report.sales.view', 'report.inventory.view', 'report.payment.view', 'report.balance.view',
            'excel_import.preview',
        ];

        $cashierPermissionKeys = [
            'dashboard.view', 'product.view', 'inventory.view',
            'sales.create', 'sales.view_own', 'payment.view',
        ];

        // Owner gets all permissions
        $ownerRole->permissions()->sync(array_column($permissionModels, 'id'));

        // Admin gets admin permissions
        $adminIds = array_map(fn ($key) => $permissionModels[$key]->id, array_filter($adminPermissionKeys, fn ($k) => isset($permissionModels[$k])));
        $adminRole->permissions()->sync($adminIds);

        // Cashier gets cashier permissions
        $cashierIds = array_map(fn ($key) => $permissionModels[$key]->id, array_filter($cashierPermissionKeys, fn ($k) => isset($permissionModels[$k])));
        $cashierRole->permissions()->sync($cashierIds);

        // 3. Superadmin User
        User::firstOrCreate(
            ['username' => 'superadmin'],
            [
                'name' => 'Superadmin',
                'password' => Hash::make('password'),
                'role_id' => $ownerRole->id,
                'status' => 'ACTIVE',
            ]
        );

        // 4. Payment Methods
        $paymentMethods = [
            ['name' => 'Cash', 'code' => 'CASH', 'type' => 'CASH'],
            ['name' => 'QRIS', 'code' => 'QRIS', 'type' => 'QRIS'],
            ['name' => 'Transfer Bank', 'code' => 'TRANSFER', 'type' => 'TRANSFER'],
            ['name' => 'E-Wallet', 'code' => 'E_WALLET', 'type' => 'E_WALLET'],
        ];
        foreach ($paymentMethods as $pm) {
            PaymentMethod::firstOrCreate(['code' => $pm['code']], $pm);
        }

        // 5. Location
        $defaultLocation = Location::firstOrCreate(
            ['code' => 'RAJA-BANGO'],
            [
                'name' => 'Raja Aksesoris Bango',
                'status' => 'ACTIVE',
            ]
        );

        User::where('username', 'superadmin')->update([
            'location_id' => $defaultLocation->id,
        ]);

        // 6. Balance Accounts
        $balanceAccounts = [
            ['name' => 'CASH', 'code' => 'CASH', 'account_type' => 'CASH', 'current_balance' => 0, 'location_id' => $defaultLocation->id],
            ['name' => 'QRIS', 'code' => 'QRIS', 'account_type' => 'QRIS', 'current_balance' => 0, 'location_id' => $defaultLocation->id],
            ['name' => 'BANK BCA', 'code' => 'BANK_BCA', 'account_type' => 'BANK', 'current_balance' => 0, 'location_id' => $defaultLocation->id],
            ['name' => 'BANK MAS', 'code' => 'BANK_MAS', 'account_type' => 'BANK', 'current_balance' => 0, 'location_id' => $defaultLocation->id],
            ['name' => 'DANA', 'code' => 'DANA', 'account_type' => 'E_WALLET', 'current_balance' => 0, 'location_id' => $defaultLocation->id],
            ['name' => 'MULTI', 'code' => 'MULTI', 'account_type' => 'PROVIDER', 'current_balance' => 0, 'location_id' => $defaultLocation->id],
            ['name' => 'WAHANA', 'code' => 'WAHANA', 'account_type' => 'PROVIDER', 'current_balance' => 0, 'location_id' => $defaultLocation->id],
        ];
        foreach ($balanceAccounts as $ba) {
            BalanceAccount::firstOrCreate(['code' => $ba['code']], $ba);
        }

        // 7. Default Settings
        $defaultSettings = [
            'store_name' => 'Raja Aksesoris',
            'receipt_paper_width' => '58mm',
            'minimum_stock_default' => '3',
            'currency' => 'Rp.',
            'timezone' => 'Asia/Jakarta',
        ];
        foreach ($defaultSettings as $key => $value) {
            Setting::set($key, $value);
        }
    }
}
