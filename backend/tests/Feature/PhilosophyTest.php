<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhilosophyTest extends TestCase
{
    use RefreshDatabase;

    public function test_philosophy_page_is_displayed(): void
    {
        $this->get(route('philosophy'))
            ->assertOk()
            ->assertSee('企業理念');
    }

    public function test_footer_links_to_philosophy_page(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('href="' . route('philosophy') . '"', false);
    }
}
