<?php

namespace Tests\Unit\Compat;

use App\Http\Controllers\API\KelasController;
use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\API\PromoController;
use App\Http\Middleware\CheckApiToken;
use App\Models\User;
use App\Services\AuthService;
use App\Services\ClassService;
use App\Services\PromoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Locks the Laravel 8 response contracts that the production frontend (arabic-go.com)
 * still depends on. The new API keeps its 'data' envelope, but these fields, status
 * codes and redirects must not change until the frontend is updated.
 */
class LegacyApiContractTest extends TestCase
{
    private User $user;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        DB::beginTransaction();

        $this->token = hash('sha256', 'legacy-contract-test');
        // user.id_bahasa defaults to 1 (FK to bahasa) and the test DB is schema-only.
        DB::table('bahasa')->insertOrIgnore(['id_bahasa' => 1, 'bahasa' => 'Bahasa Indonesia']);
        $id = DB::table('user')->insertGetId([
            'name' => 'Legacy Contract',
            'email' => 'legacy.contract@mail.test',
            'api_token' => $this->token,
            'type' => 'student',
        ], 'id_user');
        $this->user = User::find($id);
    }

    protected function tearDown(): void
    {
        DB::rollBack();
        Mockery::close();
        parent::tearDown();
    }

    private function body($response): array
    {
        return json_decode($response->getContent(), true);
    }

    private function oauthResult(): array
    {
        return [
            'success' => true,
            'user' => $this->user,
            'data' => ['api_token' => $this->token, 'name' => 'Legacy Contract', 'email' => 'legacy.contract@mail.test', 'img' => 'https://x/a.png'],
        ];
    }

    #[Test]
    public function login_success_exposes_credentials_at_top_level_and_under_data()
    {
        $auth = Mockery::mock(AuthService::class);
        $auth->shouldReceive('login')->once()->andReturn($this->oauthResult());

        $response = (new LoginController($auth))->login(Request::create('/login', 'POST', [
            'email' => 'legacy.contract@mail.test',
            'password' => 'Secret123!',
        ]));

        $body = $this->body($response);
        $this->assertSame(200, $response->getStatusCode());
        foreach (['api_token', 'name', 'email', 'img'] as $key) {
            $this->assertArrayHasKey($key, $body, "top-level $key is read by the frontend");
            $this->assertSame($body[$key], $body['data'][$key]);
        }
        $this->assertSame($this->token, $body['api_token']);
    }

    #[Test]
    public function login_empty_input_keeps_string_message()
    {
        $auth = Mockery::mock(AuthService::class);
        $response = (new LoginController($auth))->login(Request::create('/login', 'POST', []));

        $this->assertSame(422, $response->getStatusCode());
        $this->assertSame('Invalid Input', $this->body($response)['message']);
    }

    #[Test]
    public function register_duplicate_email_is_http_200_like_before()
    {
        $auth = Mockery::mock(AuthService::class);
        $auth->shouldReceive('register')->once()->andReturn(['success' => false, 'code' => 409, 'message' => 'email sudah ada']);

        $response = (new LoginController($auth))->registerEmail(Request::create('/register', 'POST', []));

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(['code' => 200, 'message' => 'email sudah ada'], array_intersect_key($this->body($response), ['code' => 1, 'message' => 1]));
    }

    #[Test]
    public function google_callback_legacy_mode_redirects_with_credentials_in_query()
    {
        config(['app-settings.oauth.legacy_redirect' => true, 'app-settings.frontend_url' => 'https://arabic-go.com']);
        Socialite::shouldReceive('driver->stateless->user')->andReturn((object) ['email' => 'legacy.contract@mail.test']);

        $auth = Mockery::mock(AuthService::class);
        $auth->shouldReceive('handleOAuthLogin')->once()->andReturn($this->oauthResult());
        $auth->shouldReceive('getOAuthRedirectUrl')->andReturn('https://arabic-go.com/student/my-class');

        $response = (new LoginController($auth))->handleProviderCallback(Request::create('/auth/callback'));

        $target = $response->getTargetUrl();
        $this->assertStringStartsWith('https://arabic-go.com/student/my-class?', $target);
        parse_str(parse_url($target, PHP_URL_QUERY), $query);
        $this->assertSame($this->token, $query['api_token']);
        $this->assertSame('Legacy Contract', $query['name']);
        $this->assertArrayNotHasKey('code', $query);
    }

    #[Test]
    public function google_callback_secure_mode_redirects_with_one_time_code_only()
    {
        config(['app-settings.oauth.legacy_redirect' => false]);
        Socialite::shouldReceive('driver->stateless->user')->andReturn((object) ['email' => 'legacy.contract@mail.test']);

        $auth = Mockery::mock(AuthService::class);
        $auth->shouldReceive('handleOAuthLogin')->once()->andReturn($this->oauthResult());
        $auth->shouldReceive('getOAuthRedirectUrl')->andReturn('https://arabic-go.com/student/my-class');

        $target = (new LoginController($auth))->handleProviderCallback(Request::create('/auth/callback'))->getTargetUrl();

        parse_str(parse_url($target, PHP_URL_QUERY), $query);
        $this->assertSame(64, strlen($query['code']));
        $this->assertArrayNotHasKey('api_token', $query);
    }

    #[Test]
    public function promo_code_keeps_legacy_codes_and_msg()
    {
        $promo = Mockery::mock(PromoService::class);
        $promo->shouldReceive('validatePromoCode')->with('OLD')->andReturn(['success' => false, 'code' => 410, 'message' => 'expired']);
        $promo->shouldReceive('validatePromoCode')->with('NOPE')->andReturn(['success' => false, 'code' => 404, 'message' => 'not found']);
        $promo->shouldReceive('validatePromoCode')->with('OK')->andReturn(['success' => true, 'type' => 'percent', 'value' => 10]);
        $controller = new PromoController($promo);

        $expired = $controller->index(Request::create('/promo-code', 'POST', ['api_token' => $this->token, 'code' => 'OLD']));
        $this->assertSame(200, $expired->getStatusCode());
        $this->assertSame(419, $this->body($expired)['code']);
        $this->assertSame('Promo Coupon Expired', $this->body($expired)['msg']);

        $invalid = $controller->index(Request::create('/promo-code', 'POST', ['api_token' => $this->token, 'code' => 'NOPE']));
        $this->assertSame(401, $invalid->getStatusCode());
        $this->assertSame('Promo Coupon Invalid', $this->body($invalid)['msg']);

        $valid = $this->body($controller->index(Request::create('/promo-code', 'POST', ['api_token' => $this->token, 'code' => 'OK'])));
        $this->assertSame('percent', $valid['type']);
        $this->assertEquals(10, $valid['value']);
    }

    #[Test]
    public function checkout_info_puts_class_fields_at_top_level()
    {
        $classes = Mockery::mock(ClassService::class);
        $classes->shouldReceive('getCheckoutInfo')->andReturn([
            'success' => true,
            'message' => 'ok',
            'data' => ['id_class' => 7, 'title' => 'Nahwu Dasar', 'price' => 150000, 'langkah' => 2],
        ]);

        $body = $this->body((new KelasController($classes))->checkoutInfoClass(
            Request::create('/class/checkout-info', 'POST', ['api_token' => $this->token, 'class' => 7])
        ));

        $this->assertSame('Nahwu Dasar', $body['title']);
        $this->assertSame(150000, $body['price']);
        $this->assertSame(7, $body['id_class']);
    }

    #[Test]
    public function kelas_check_is_always_http_200_with_outcome_in_body()
    {
        $classes = Mockery::mock(ClassService::class);
        $classes->shouldReceive('checkEnrollment')->andReturn(['enrolled' => false, 'code' => 1919, 'message' => "Haven't enlisted yet"]);

        $response = (new KelasController($classes))->checkKelas(
            Request::create('/kelas/check', 'POST', ['api_token' => $this->token, 'kelas' => 9])
        );

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(1919, $this->body($response)['code']);
    }

    #[Test]
    public function token_middleware_keeps_legacy_message()
    {
        $response = (new CheckApiToken())->handle(Request::create('/profile', 'POST'), fn () => response('next'));

        $this->assertSame(401, $response->getStatusCode());
        $this->assertSame('Invalid Token! Access Prohibited', $this->body($response)['message']);
    }
}
