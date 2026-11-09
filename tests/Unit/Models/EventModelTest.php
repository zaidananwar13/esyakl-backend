<?php

namespace Tests\Unit\Models;

use PHPUnit\Framework\Attributes\Test;

use App\Models\Event;
use Tests\TestCase;

class EventModelTest extends TestCase
{
    #[Test]
    public function it_can_be_instantiated()
    {
        $event = new Event();
        $this->assertInstanceOf(Event::class, $event);
    }

    #[Test]
    public function it_can_set_and_get_attributes()
    {
        $event = new Event();
        
        $event->title = 'Test Event';
        $event->description = 'Test description';
        $event->owner = 1;
        $event->quota = 100;
        $event->image = 'event.jpg';
        $event->status = 'active';

        $this->assertEquals('Test Event', $event->title);
        $this->assertEquals('Test description', $event->description);
        $this->assertEquals(1, $event->owner);
        $this->assertEquals(100, $event->quota);
        $this->assertEquals('event.jpg', $event->image);
        $this->assertEquals('active', $event->status);
    }

    #[Test]
    public function it_extends_model()
    {
        $event = new Event();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Model::class, $event);
    }
}
