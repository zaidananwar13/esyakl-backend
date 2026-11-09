<?php

namespace Tests\Unit\Models;

use PHPUnit\Framework\Attributes\Test;

use App\Models\ClassStudent;
use Tests\TestCase;

class ClassStudentModelTest extends TestCase
{
    #[Test]
    public function it_can_be_instantiated()
    {
        $classStudent = new ClassStudent();
        $this->assertInstanceOf(ClassStudent::class, $classStudent);
    }

    #[Test]
    public function it_can_set_and_get_attributes()
    {
        $classStudent = new ClassStudent();
        
        $classStudent->id_user = 1;
        $classStudent->id_class = 2;
        $classStudent->expired_at = 1234567890;
        $classStudent->status_class = 'On';
        $classStudent->completed_at = null;

        $this->assertEquals(1, $classStudent->id_user);
        $this->assertEquals(2, $classStudent->id_class);
        $this->assertEquals(1234567890, $classStudent->expired_at);
        $this->assertEquals('On', $classStudent->status_class);
        $this->assertNull($classStudent->completed_at);
    }

    #[Test]
    public function it_extends_model()
    {
        $classStudent = new ClassStudent();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Model::class, $classStudent);
    }
}
