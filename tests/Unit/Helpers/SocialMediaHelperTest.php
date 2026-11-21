<?php

namespace Tests\Unit\Helpers;

use App\Helpers\SocialMediaHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class SocialMediaHelperTest extends TestCase
{
    #[Test]
    public function null_or_invalid_json_returns_all_keys_as_null()
    {
        $empty = ['linkedin' => null, 'facebook' => null, 'instagram' => null];

        $this->assertSame($empty, SocialMediaHelper::normalize(null));
        $this->assertSame($empty, SocialMediaHelper::normalize(''));
        $this->assertSame($empty, SocialMediaHelper::normalize('not json'));
        $this->assertSame($empty, SocialMediaHelper::normalize('{"linkedin":"","instagram":"","facebook":""}'));
    }

    #[Test]
    public function instagram_handle_from_production_data_becomes_full_url()
    {
        $result = SocialMediaHelper::normalize('{"linkedin":"","instagram":"@salmanananda.ms","facebook":""}');

        $this->assertSame('https://www.instagram.com/salmanananda.ms', $result['instagram']);
        $this->assertNull($result['linkedin']);
        $this->assertNull($result['facebook']);
    }

    #[Test]
    public function accepts_decoded_object_and_array_input()
    {
        $this->assertSame(
            'https://www.facebook.com/someone',
            SocialMediaHelper::normalize((object) ['facebook' => 'someone'])['facebook']
        );
        $this->assertSame(
            'https://www.linkedin.com/in/someone',
            SocialMediaHelper::normalize(['linkedin' => 'someone'])['linkedin']
        );
    }

    public static function urlProvider(): array
    {
        return [
            'full https url kept' => ['instagram', 'https://www.instagram.com/abc/', 'https://www.instagram.com/abc/'],
            'http upgraded to https' => ['facebook', 'http://facebook.com/abc', 'https://facebook.com/abc'],
            'scheme-less www url' => ['linkedin', 'www.linkedin.com/in/abc', 'https://www.linkedin.com/in/abc'],
            'scheme-less bare domain' => ['instagram', 'instagram.com/abc', 'https://instagram.com/abc'],
            'subdomain allowed' => ['facebook', 'https://m.facebook.com/abc', 'https://m.facebook.com/abc'],
            'fb.com allowed for facebook' => ['facebook', 'https://fb.com/abc', 'https://fb.com/abc'],
            'query kept' => ['facebook', 'https://www.facebook.com/profile.php?id=123', 'https://www.facebook.com/profile.php?id=123'],
            'handle with slash' => ['instagram', '@abc/', 'https://www.instagram.com/abc'],
            'whitespace trimmed' => ['instagram', '  abc  ', 'https://www.instagram.com/abc'],
        ];
    }

    #[Test]
    #[DataProvider('urlProvider')]
    public function valid_values_are_normalized(string $platform, string $input, string $expected)
    {
        $this->assertSame($expected, SocialMediaHelper::normalize([$platform => $input])[$platform]);
    }

    public static function rejectedProvider(): array
    {
        return [
            'javascript scheme' => ['instagram', 'javascript:alert(1)'],
            'javascript url form' => ['instagram', 'javascript://instagram.com/%0aalert(1)'],
            'data scheme' => ['facebook', 'data:text/html,<script>alert(1)</script>'],
            'foreign domain' => ['instagram', 'https://evil.example/instagram.com'],
            'lookalike domain' => ['facebook', 'https://notfacebook.com/abc'],
            'wrong platform domain' => ['linkedin', 'https://www.instagram.com/abc'],
            'handle with spaces' => ['instagram', 'my handle'],
            'handle with html' => ['instagram', '"><script>'],
            'non-string value' => ['instagram', ['x']],
        ];
    }

    #[Test]
    #[DataProvider('rejectedProvider')]
    public function unsafe_or_foreign_values_become_null(string $platform, mixed $input)
    {
        $this->assertNull(SocialMediaHelper::normalize([$platform => $input])[$platform]);
    }
}
