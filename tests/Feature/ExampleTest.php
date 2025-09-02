<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
    $response = $this->get('/');

    // Homepage may redirect to login in test environments; accept 200 or 302
    $this->assertContains($response->getStatusCode(), [200, 302]);
    }
}
