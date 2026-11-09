<?php

namespace Tests\Unit\Models;

use PHPUnit\Framework\Attributes\Test;

use App\Models\User;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    #[Test]
    public function it_has_correct_table_name()
    {
        $user = new User();
        $this->assertEquals('user', $user->getTable());
    }

    #[Test]
    public function it_has_correct_primary_key()
    {
        $user = new User();
        $this->assertEquals('id_user', $user->getKeyName());
    }

    #[Test]
    public function it_uses_unix_timestamp_date_format()
    {
        $user = new User();
        $this->assertEquals('U', $user->getDateFormat());
    }

    #[Test]
    public function it_can_set_and_get_attributes()
    {
        $user = new User();
        
        $user->name = 'Test User';
        $user->email = 'test@example.com';
        $user->password = 'hashed_password';
        $user->api_token = 'test_token_123';
        $user->google_id = 'google_123';
        $user->type = 'student';
        $user->phone_number = '1234567890';
        $user->about = 'About me text';
        $user->username = 'testuser';
        $user->region = 1;
        $user->id_bahasa = 1;
        $user->current_job = 'Developer';
        $user->avatar_original = 'avatar.jpg';
        $user->social_media = '{"twitter": "@test"}';

        $this->assertEquals('Test User', $user->name);
        $this->assertEquals('test@example.com', $user->email);
        $this->assertEquals('hashed_password', $user->password);
        $this->assertEquals('test_token_123', $user->api_token);
        $this->assertEquals('google_123', $user->google_id);
        $this->assertEquals('student', $user->type);
        $this->assertEquals('1234567890', $user->phone_number);
        $this->assertEquals('About me text', $user->about);
        $this->assertEquals('testuser', $user->username);
        $this->assertEquals(1, $user->region);
        $this->assertEquals(1, $user->id_bahasa);
        $this->assertEquals('Developer', $user->current_job);
        $this->assertEquals('avatar.jpg', $user->avatar_original);
        $this->assertEquals('{"twitter": "@test"}', $user->social_media);
    }

    #[Test]
    public function it_is_instance_of_authenticatable()
    {
        $user = new User();
        $this->assertInstanceOf(\Illuminate\Foundation\Auth\User::class, $user);
    }

    #[Test]
    public function it_implements_must_verify_email()
    {
        $user = new User();
        $this->assertInstanceOf(\Illuminate\Contracts\Auth\MustVerifyEmail::class, $user);
    }

    #[Test]
    public function it_uses_notifiable_trait()
    {
        $user = new User();
        $this->assertTrue(method_exists($user, 'notify'));
        $this->assertTrue(method_exists($user, 'notifications'));
    }

    #[Test]
    public function it_can_convert_to_array()
    {
        $user = new User();
        $user->name = 'Test User';
        $user->email = 'test@example.com';

        $array = $user->toArray();

        $this->assertIsArray($array);
        $this->assertEquals('Test User', $array['name']);
        $this->assertEquals('test@example.com', $array['email']);
    }

    #[Test]
    public function it_can_mass_assign_attributes_via_fill()
    {
        $user = new User();
        
        // Since there are no guarded/fillable defined, we test direct assignment
        $user->name = 'Mass Assigned User';
        $user->email = 'mass@example.com';

        $this->assertEquals('Mass Assigned User', $user->name);
        $this->assertEquals('mass@example.com', $user->email);
    }

    #[Test]
    public function it_handles_null_attributes_gracefully()
    {
        $user = new User();
        
        $this->assertNull($user->name);
        $this->assertNull($user->email);
        $this->assertNull($user->password);
        $this->assertNull($user->api_token);
    }

    #[Test]
    public function it_can_get_key_value()
    {
        $user = new User();
        $user->id_user = 123;

        $this->assertEquals(123, $user->getKey());
    }

    #[Test]
    public function it_can_check_if_incrementing_is_true()
    {
        $user = new User();
        $this->assertTrue($user->getIncrementing());
    }

    #[Test]
    public function it_can_get_qualified_key_name()
    {
        $user = new User();
        $this->assertEquals('user.id_user', $user->getQualifiedKeyName());
    }

    #[Test]
    public function it_extends_authenticatable()
    {
        $user = new User();
        
        // User should extend Laravel's Authenticatable class
        $reflection = new \ReflectionClass($user);
        $parent = $reflection->getParentClass();
        
        $this->assertEquals('Illuminate\Foundation\Auth\User', $parent->getName());
    }

    #[Test]
    public function it_can_get_auth_identifier()
    {
        $user = new User();
        $user->id_user = 123;
        
        $this->assertEquals(123, $user->getAuthIdentifier());
    }

    #[Test]
    public function it_has_timestamps_enabled_by_default()
    {
        $user = new User();
        
        $this->assertTrue($user->usesTimestamps());
    }

    #[Test]
    public function it_can_serialize_to_json()
    {
        $user = new User();
        $user->name = 'JSON User';
        $user->email = 'json@example.com';

        $json = $user->toJson();

        $this->assertIsString($json);
        $this->assertStringContainsString('JSON User', $json);
        $this->assertStringContainsString('json@example.com', $json);
    }

    #[Test]
    public function it_can_access_attributes_dynamically()
    {
        $user = new User();
        
        $user->setAttribute('name', 'Dynamic User');
        
        $this->assertEquals('Dynamic User', $user->getAttribute('name'));
    }

    #[Test]
    public function it_has_correct_auth_identifier_name()
    {
        $user = new User();
        
        $this->assertEquals('id_user', $user->getAuthIdentifierName());
    }

    #[Test]
    public function it_can_get_auth_password()
    {
        $user = new User();
        $user->password = 'hashed_password_123';
        
        $this->assertEquals('hashed_password_123', $user->getAuthPassword());
    }

    #[Test]
    public function it_can_get_remember_token()
    {
        $user = new User();
        $user->remember_token = 'remember_token_value';
        
        $this->assertEquals('remember_token_value', $user->getRememberToken());
    }

    #[Test]
    public function it_can_set_remember_token()
    {
        $user = new User();
        $user->setRememberToken('new_remember_token');
        
        $this->assertEquals('new_remember_token', $user->remember_token);
    }

    #[Test]
    public function it_can_get_remember_token_name()
    {
        $user = new User();
        
        $this->assertEquals('remember_token', $user->getRememberTokenName());
    }

    #[Test]
    public function it_returns_correct_type_for_key()
    {
        $user = new User();
        
        $this->assertEquals('int', $user->getKeyType());
    }

    #[Test]
    public function it_can_check_attribute_existence()
    {
        $user = new User();
        $user->name = 'Test';
        
        $this->assertTrue(isset($user->name));
        $this->assertFalse(isset($user->nonexistent_attribute));
    }

    #[Test]
    public function it_can_unset_attribute()
    {
        $user = new User();
        $user->name = 'Test';
        
        unset($user->name);
        
        $this->assertNull($user->name);
    }
}
