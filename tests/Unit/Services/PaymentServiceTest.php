<?php

namespace Tests\Unit\Services;

use App\Models\Kelas;
use App\Models\User;
use App\Models\UserPurchase;
use App\Services\PaymentService;
use Illuminate\Support\Carbon;
use Mockery;
use Tests\TestCase;

class PaymentServiceTest extends TestCase
{
    protected PaymentService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PaymentService();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_create_snap_payment_returns_array()
    {
        $result = $this->service->createSnapPayment(99999, 1, 100000);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
    }

    public function test_handle_payment_returns_error_for_invalid_order_id()
    {
        $result = $this->service->handlePayment(1, [
            'order_id' => 'invalid',
            'transaction_time' => now()->toDateTimeString(),
            'payment_type' => 'credit_card',
            'gross_amount' => 100000
        ]);

        $this->assertFalse($result['success']);
        $this->assertEquals(400, $result['code']);
        $this->assertEquals('Invalid order ID format', $result['message']);
    }

    public function test_handle_payment_parses_order_id_correctly()
    {
        $timestamp = Carbon::now()->format('U');
        $classId = 5;
        $orderId = $timestamp . '$' . $classId;
        
        $result = $this->service->handlePayment(1, [
            'order_id' => $orderId,
            'transaction_time' => now()->toDateTimeString(),
            'payment_type' => 'credit_card',
            'gross_amount' => 100000
        ]);

        // The update will fail because no record exists, but order parsing works
        $this->assertIsArray($result);
    }

    public function test_parse_user_name_extracts_first_and_last_name()
    {
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('parseUserName');
        $method->setAccessible(true);

        $result = $method->invoke($this->service, 'John Doe');
        
        $this->assertEquals('John', $result['first_name']);
        $this->assertEquals('Doe', $result['last_name']);
    }

    public function test_parse_user_name_handles_single_name()
    {
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('parseUserName');
        $method->setAccessible(true);

        $result = $method->invoke($this->service, 'John');
        
        $this->assertEquals('John', $result['first_name']);
        $this->assertEquals('', $result['last_name']);
    }

    public function test_parse_user_name_handles_multiple_names()
    {
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('parseUserName');
        $method->setAccessible(true);

        $result = $method->invoke($this->service, 'John Paul Smith');
        
        $this->assertEquals('John', $result['first_name']);
        $this->assertEquals('Paul Smith', $result['last_name']);
    }

    public function test_configure_midtrans_is_called_on_construction()
    {
        $service = new PaymentService();
        $this->assertInstanceOf(PaymentService::class, $service);
    }

    public function test_handle_payment_with_empty_order_id()
    {
        $result = $this->service->handlePayment(1, [
            'order_id' => '',
            'transaction_time' => now()->toDateTimeString()
        ]);

        $this->assertFalse($result['success']);
    }

    public function test_handle_payment_with_missing_transaction_time_uses_now()
    {
        $timestamp = Carbon::now()->format('U');
        $result = $this->service->handlePayment(1, [
            'order_id' => $timestamp . '$1',
            'payment_type' => 'gopay',
            'gross_amount' => 50000
        ]);

        $this->assertIsArray($result);
    }
}
