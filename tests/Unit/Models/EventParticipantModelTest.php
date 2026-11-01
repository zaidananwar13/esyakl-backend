<?php

namespace Tests\Unit\Models;

use App\Models\EventParticipant;
use Tests\TestCase;

class EventParticipantModelTest extends TestCase
{
    public function test_model_can_be_instantiated()
    {
        $eventParticipant = new EventParticipant();
        $this->assertInstanceOf(EventParticipant::class, $eventParticipant);
    }

    public function test_model_uses_has_factory_trait()
    {
        $this->assertTrue(method_exists(EventParticipant::class, 'factory'));
    }

    public function test_can_set_attributes()
    {
        $eventParticipant = new EventParticipant();
        $eventParticipant->id_event = 1;
        $eventParticipant->id_user = 5;

        $this->assertEquals(1, $eventParticipant->id_event);
        $this->assertEquals(5, $eventParticipant->id_user);
    }
}
