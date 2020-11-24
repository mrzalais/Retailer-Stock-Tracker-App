<?php


namespace Tests\Clients;


use Exception;
use Tests\TestCase;
use App\Models\Stock;
use App\Clients\BestBuy;
use Illuminate\Support\Facades\Http;
use Database\Seeders\RetailerWithProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BestBuyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_tracks_a_product(): void
    {
        $this->seed(RetailerWithProductSeeder::class);

        $stock = tap(Stock::first())->update([
            'sku' => '6364253',
            'url' => 'https://www.bestbuy.com/site/nintendo-switch-32gb-console-gray-joy-con/6364253.p?skuId=6364253'
        ]);

        try {
            (new BestBuy())->checkAvailability($stock);
        } catch (Exception $e) {
            $this->fail('Failed to track the BestBuy API properly.' . $e->getMessage());
        }

        $this->assertTrue(true);
    }

    /** @test */
    public function it_creates_the_proper_stock_status_response(): void
    {
        Http::fake(fn() => ['salePrice' => 299.99, 'onlineAvailability' => true]);

        $stockStatus = (new BestBuy())->checkAvailability(new Stock);

        $this->assertEquals(29999, $stockStatus->price);
        $this->assertTrue($stockStatus->available);
    }
}
