<?php

namespace Tests\Unit\Security;

use App\Http\Middleware\CheckApiToken;
use App\Services\PaymentService;
use App\Services\QuizService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Regression tests for the security fixes made during the Laravel 13 upgrade.
 * Runs against the schema-only "arabic_go_test" database; every test rolls back.
 */
class SecurityRegressionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::beginTransaction();
    }

    protected function tearDown(): void
    {
        DB::rollBack();
        parent::tearDown();
    }

    private function paymentServiceReturning(?object $status): PaymentService
    {
        config(['services.midtrans.server_key' => 'test-server-key']);

        return new class($status) extends PaymentService {
            public function __construct(private ?object $fakeStatus)
            {
                parent::__construct();
            }

            protected function fetchTransactionStatus(string $orderId): object
            {
                if ($this->fakeStatus === null) {
                    throw new \RuntimeException('Midtrans unreachable');
                }

                return $this->fakeStatus;
            }
        };
    }

    #[Test]
    public function payment_is_rejected_when_gateway_is_not_configured(): void
    {
        config(['services.midtrans.server_key' => null]);

        $result = (new PaymentService())->verifyTransaction('1700000000$35');

        $this->assertFalse($result['success']);
        $this->assertSame(503, $result['code']);
    }

    #[Test]
    public function payment_is_rejected_when_midtrans_says_pending(): void
    {
        $service = $this->paymentServiceReturning((object) [
            'order_id' => '1700000000$35',
            'transaction_status' => 'pending',
        ]);

        $result = $service->verifyTransaction('1700000000$35');

        $this->assertFalse($result['success']);
        $this->assertSame(402, $result['code']);
    }

    #[Test]
    public function payment_is_rejected_when_capture_is_flagged_as_fraud(): void
    {
        $service = $this->paymentServiceReturning((object) [
            'order_id' => '1700000000$35',
            'transaction_status' => 'capture',
            'fraud_status' => 'challenge',
        ]);

        $this->assertFalse($service->verifyTransaction('1700000000$35')['success']);
    }

    #[Test]
    public function payment_is_rejected_when_midtrans_returns_another_order(): void
    {
        $service = $this->paymentServiceReturning((object) [
            'order_id' => '1699999999$35',
            'transaction_status' => 'settlement',
        ]);

        $this->assertFalse($service->verifyTransaction('1700000000$35')['success']);
    }

    #[Test]
    public function payment_is_rejected_when_midtrans_is_unreachable(): void
    {
        $result = $this->paymentServiceReturning(null)->verifyTransaction('1700000000$35');

        $this->assertFalse($result['success']);
        $this->assertSame(402, $result['code']);
    }

    #[Test]
    public function settled_payment_is_accepted(): void
    {
        $service = $this->paymentServiceReturning((object) [
            'order_id' => '1700000000$35',
            'transaction_status' => 'settlement',
            'gross_amount' => '1000.00',
        ]);

        $this->assertTrue($service->verifyTransaction('1700000000$35')['success']);
    }

    #[Test]
    public function forged_finish_payment_never_reaches_the_database_update(): void
    {
        // Client claims success, but Midtrans reports the order as pending.
        $service = $this->paymentServiceReturning((object) [
            'order_id' => '1700000000$35',
            'transaction_status' => 'pending',
        ]);

        $result = $service->handlePayment(1, [
            'order_id' => '1700000000$35',
            'payment_type' => 'gopay',
            'gross_amount' => 1000,
        ]);

        $this->assertFalse($result['success']);
        $this->assertSame(402, $result['code']);
    }

    #[Test]
    public function non_numeric_order_parts_are_rejected(): void
    {
        $result = (new PaymentService())->handlePayment(1, ['order_id' => 'abc$1 OR 1=1']);

        $this->assertFalse($result['success']);
        $this->assertSame(400, $result['code']);
    }

    #[Test]
    public function bearer_only_request_exposes_the_verified_token_to_controllers(): void
    {
        $token = hash('sha256', 'security-regression');
        $bahasaId = DB::table('bahasa')->insertGetId(['bahasa' => 'Test'], 'id_bahasa');
        $userId = DB::table('user')->insertGetId([
            'id_bahasa' => $bahasaId,
            'name' => 'Bearer Test',
            'email' => 'bearer-test@example.test',
            'type' => 'student',
            'api_token' => $token,
            'created_at' => time(),
            'updated_at' => time(),
        ], 'id_user');

        $request = Request::create('/v2/profile', 'POST');
        $request->headers->set('Authorization', 'Bearer ' . $token);

        $seenToken = null;
        $response = (new CheckApiToken())->handle($request, function (Request $r) use (&$seenToken) {
            $seenToken = $r->input('api_token');

            return response('ok');
        });

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame($token, $seenToken, 'Controllers must never see a null api_token');
        $this->assertSame($userId, $request->user()->id_user);
    }

    #[Test]
    public function public_option_endpoint_does_not_expose_the_answer_key(): void
    {
        $query = null;
        DB::listen(function ($q) use (&$query) {
            $query = $q->sql;
        });

        app(QuizService::class)->getOptionById(1);

        $this->assertNotNull($query);
        $this->assertStringNotContainsString('is_true', $query);
        $this->assertStringNotContainsString('*', $query);
    }

    #[Test]
    public function user_dump_and_debug_routes_are_not_registered(): void
    {
        $uris = collect(Route::getRoutes()->getRoutes())->map->uri();

        $this->assertNotContains('getusertoken', $uris);
        $this->assertNotContains('v2/getusertoken', $uris);
        $this->assertNotContains('notification/handle', $uris);
    }
}
