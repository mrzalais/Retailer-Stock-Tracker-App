<?php

namespace Tests\Unit;

use App\Clients\Client;
use App\Clients\ClientException;
use App\Clients\StockStatus;
use App\Models\Stock;
use App\Models\Retailer;
use Facades\App\Clients\ClientFactory;
use Tests\TestCase;
use Database\Seeders\RetailerWithProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;


class StockTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_throws_an_exception_if_a_client_is_not_found_when_tracking()
    {
        $this->seed(RetailerWithProductSeeder::class);

        Retailer::first()->update(['name' => 'Foo Retailer']);

        $this->expectException(ClientException::class);

        Stock::first()->track();
    }

    /** @test */
    public function it_updates_local_stock_status_after_being_tracked()
    {
        $this->seed(RetailerWithProductSeeder::class);

        $this->mockClientRequest($available = true, $price = 9900);

        $stock = tap(Stock::first())->track();

        self::assertTrue($stock->in_stock);

        self::assertEquals(9900, $stock->price);
    }
}


