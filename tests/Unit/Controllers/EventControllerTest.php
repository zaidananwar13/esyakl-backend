<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\API\EventController;
use App\Services\EventService;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class EventControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_controller_can_be_instantiated()
    {
        $mockService = Mockery::mock(EventService::class);
        $controller = new EventController($mockService);
        $this->assertInstanceOf(EventController::class, $controller);
    }

    public function test_event_today_returns_events()
    {
        $events = [['id' => 1, 'title' => 'Today Event']];

        $mockService = Mockery::mock(EventService::class);
        $mockService->shouldReceive('getUpcomingEvents')->once()->andReturn($events);

        $controller = new EventController($mockService);
        $request = new Request();

        $response = $controller->eventToday($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_event_today_returns_not_found_when_empty()
    {
        $mockService = Mockery::mock(EventService::class);
        $mockService->shouldReceive('getUpcomingEvents')->once()->andReturn([]);

        $controller = new EventController($mockService);
        $request = new Request();

        $response = $controller->eventToday($request);

        $this->assertEquals(404, $response->getStatusCode());
    }
}
