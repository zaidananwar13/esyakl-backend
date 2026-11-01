<?php

namespace Tests\Unit\Models;

use App\Models\Faq;
use Tests\TestCase;

class FaqModelTest extends TestCase
{
    public function test_model_can_be_instantiated()
    {
        $faq = new Faq();
        $this->assertInstanceOf(Faq::class, $faq);
    }

    public function test_model_has_correct_table_name()
    {
        $faq = new Faq();
        $this->assertEquals('faq', $faq->getTable());
    }

    public function test_model_uses_has_factory_trait()
    {
        $this->assertTrue(method_exists(Faq::class, 'factory'));
    }

    public function test_can_set_attributes()
    {
        $faq = new Faq();
        $faq->question = 'What is this?';
        $faq->answere = 'This is an answer.';

        $this->assertEquals('What is this?', $faq->question);
        $this->assertEquals('This is an answer.', $faq->answere);
    }
}
