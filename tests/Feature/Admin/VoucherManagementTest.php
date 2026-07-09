<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;

class VoucherManagementTest extends TestCase
{
    public function test_admin_can_view_voucher_list()
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get('/admin/vouchers');

        $response->assertStatus(200);
    }

    public function test_admin_can_create_nominal_voucher()
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post('/admin/vouchers', [
            'code' => 'DISC10K',
            'type' => 'nominal',
            'value' => 10000,
            'min_purchase' => 50000,
            'max_usage' => 100,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('vouchers', [
            'code' => 'DISC10K',
            'type' => 'nominal',
            'value' => 10000,
        ]);
    }

    public function test_admin_can_create_percentage_voucher()
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post('/admin/vouchers', [
            'code' => 'DISC10',
            'type' => 'percentage',
            'value' => 10,
            'min_purchase' => 50000,
            'max_usage' => 100,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('vouchers', [
            'code' => 'DISC10',
            'type' => 'percentage',
            'value' => 10,
        ]);
    }

    public function test_admin_can_edit_voucher()
    {
        $admin = $this->createAdmin();
        $voucher = $this->createVoucher(['code' => 'OLDCODE']);

        $response = $this->actingAs($admin)->put('/admin/vouchers/' . $voucher->id, [
            'code' => 'NEWCODE',
            'type' => 'nominal',
            'value' => 20000,
            'min_purchase' => 100000,
            'max_usage' => 50,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('vouchers', [
            'id' => $voucher->id,
            'code' => 'NEWCODE',
            'value' => 20000,
        ]);
    }

    public function test_admin_can_delete_voucher()
    {
        $admin = $this->createAdmin();
        $voucher = $this->createVoucher();

        $response = $this->actingAs($admin)->delete('/admin/vouchers/' . $voucher->id);

        $response->assertRedirect();
        $this->assertDatabaseMissing('vouchers', [
            'id' => $voucher->id,
        ]);
    }

    public function test_create_fails_with_duplicate_code()
    {
        $admin = $this->createAdmin();
        $this->createVoucher(['code' => 'DUPLICATE']);

        $response = $this->actingAs($admin)->post('/admin/vouchers', [
            'code' => 'DUPLICATE',
            'type' => 'nominal',
            'value' => 10000,
            'min_purchase' => 50000,
            'max_usage' => 100,
        ]);

        $response->assertSessionHasErrors('code');
    }

    public function test_create_fails_with_percentage_value_greater_than_100()
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post('/admin/vouchers', [
            'code' => 'INVALID',
            'type' => 'percentage',
            'value' => 150, // Should be max 100
            'min_purchase' => 50000,
            'max_usage' => 100,
        ]);

        $response->assertSessionHasErrors('value');
    }

    public function test_used_count_is_shown_correctly()
    {
        $admin = $this->createAdmin();
        $this->createVoucher(['code' => 'TESTCODE', 'used_count' => 42]);

        $response = $this->actingAs($admin)->get('/admin/vouchers');

        $response->assertSee('42');
    }
}
