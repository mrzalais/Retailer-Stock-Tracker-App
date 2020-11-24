<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use Illuminate\Support\Facades\Notification;
use Database\Seeders\RetailerWithProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TrackCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();

        $this->seed(RetailerWithProductSeeder::class);
    }

    /** @test */
    public function it_tracks_product_stock(): void
    {
        self::assertFalse(Product::first()->inStock());

        $this->mockClientRequest();

        $this->artisan('track');

        self::assertTrue(Product::first()->inStock());
    }
}
