<?php

namespace Tests\Unit\Models;

use PHPUnit\Framework\Attributes\Test;

use App\Models\Kelas;
use Tests\TestCase;

class KelasModelTest extends TestCase
{
    #[Test]
    public function it_has_correct_table_name()
    {
        $kelas = new Kelas();
        $this->assertEquals('class', $kelas->getTable());
    }

    #[Test]
    public function it_has_correct_primary_key()
    {
        $kelas = new Kelas();
        $this->assertEquals('id_class', $kelas->getKeyName());
    }

    #[Test]
    public function it_can_set_and_get_attributes()
    {
        $kelas = new Kelas();
        
        $kelas->title = 'Test Class';
        $kelas->image = 'test.jpg';
        $kelas->description = 'Test description';
        $kelas->id_kategori = 1;

        $this->assertEquals('Test Class', $kelas->title);
        $this->assertEquals('test.jpg', $kelas->image);
        $this->assertEquals('Test description', $kelas->description);
        $this->assertEquals(1, $kelas->id_kategori);
    }

    #[Test]
    public function it_can_convert_to_array()
    {
        $kelas = new Kelas();
        $kelas->title = 'Test Class';

        $array = $kelas->toArray();

        $this->assertIsArray($array);
        $this->assertEquals('Test Class', $array['title']);
    }

    #[Test]
    public function it_extends_model()
    {
        $kelas = new Kelas();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Model::class, $kelas);
    }
}
