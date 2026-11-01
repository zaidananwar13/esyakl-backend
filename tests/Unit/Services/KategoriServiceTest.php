<?php

namespace Tests\Unit\Services;

use App\Models\Kategori;
use App\Services\KategoriService;
use Mockery;
use Tests\TestCase;

class KategoriServiceTest extends TestCase
{
    protected KategoriService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new KategoriService();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_all_kategori_returns_collection()
    {
        $result = $this->service->getAllKategori();
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $result);
    }

    public function test_service_can_be_instantiated()
    {
        $service = new KategoriService();
        $this->assertInstanceOf(KategoriService::class, $service);
    }

    public function test_get_all_kategori_returns_kategori_with_attributes()
    {
        $result = $this->service->getAllKategori();
        
        // Verify it's a collection
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $result);
        
        // If there are results, check structure
        if ($result->count() > 0) {
            $first = $result->first();
            $this->assertTrue(property_exists($first, 'judul') || isset($first->judul));
        }
    }
}
