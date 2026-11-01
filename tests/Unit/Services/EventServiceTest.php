<?php

namespace Tests\Unit\Services;

use App\Services\EventService;
use Tests\TestCase;

class EventServiceTest extends TestCase
{
    protected EventService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new EventService();
    }

    /** @test */
    public function get_event_details_returns_array()
    {
        $this->assertTrue(method_exists($this->service, 'getEventDetails'));
        
        $reflection = new \ReflectionMethod($this->service, 'getEventDetails');
        $this->assertEquals('array', $reflection->getReturnType()->getName());
    }

    /** @test */
    public function get_user_event_status_returns_string()
    {
        $this->assertTrue(method_exists($this->service, 'getUserEventStatus'));
        
        $reflection = new \ReflectionMethod($this->service, 'getUserEventStatus');
        $this->assertEquals('string', $reflection->getReturnType()->getName());
    }

    /** @test */
    public function get_active_events_returns_array()
    {
        $this->assertTrue(method_exists($this->service, 'getActiveEvents'));
        
        $reflection = new \ReflectionMethod($this->service, 'getActiveEvents');
        $this->assertEquals('array', $reflection->getReturnType()->getName());
    }

    /** @test */
    public function get_upcoming_events_returns_array()
    {
        $this->assertTrue(method_exists($this->service, 'getUpcomingEvents'));
        
        $reflection = new \ReflectionMethod($this->service, 'getUpcomingEvents');
        $this->assertEquals('array', $reflection->getReturnType()->getName());
    }

    /** @test */
    public function get_user_event_history_returns_array()
    {
        $this->assertTrue(method_exists($this->service, 'getUserEventHistory'));
        
        $reflection = new \ReflectionMethod($this->service, 'getUserEventHistory');
        $this->assertEquals('array', $reflection->getReturnType()->getName());
    }

    /** @test */
    public function attend_event_returns_array()
    {
        $this->assertTrue(method_exists($this->service, 'attendEvent'));
        
        $reflection = new \ReflectionMethod($this->service, 'attendEvent');
        $this->assertEquals('array', $reflection->getReturnType()->getName());
    }

    /** @test */
    public function attend_event_accepts_event_id_and_user_id()
    {
        $reflection = new \ReflectionMethod($this->service, 'attendEvent');
        $params = $reflection->getParameters();
        
        $this->assertEquals('eventId', $params[0]->getName());
        $this->assertEquals('int', $params[0]->getType()->getName());
        $this->assertEquals('userId', $params[1]->getName());
        $this->assertEquals('int', $params[1]->getType()->getName());
    }

    /** @test */
    public function get_event_by_id_returns_nullable_array()
    {
        $this->assertTrue(method_exists($this->service, 'getEventById'));
        
        $reflection = new \ReflectionMethod($this->service, 'getEventById');
        $this->assertTrue($reflection->getReturnType()->allowsNull());
    }

    /** @test */
    public function all_public_methods_exist()
    {
        $methods = [
            'getEventDetails',
            'getUserEventStatus',
            'getActiveEvents',
            'getUpcomingEvents',
            'getUserEventHistory',
            'attendEvent',
            'getEventById',
        ];

        foreach ($methods as $method) {
            $this->assertTrue(method_exists($this->service, $method), "Method $method should exist");
        }
    }
}
