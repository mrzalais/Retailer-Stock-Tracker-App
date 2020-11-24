<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class TrackCommand extends Command
{
    protected $signature = 'track';

    protected $description = 'Tack all product stock';

    public function handle(): void
    {
        Product::all()
            ->tap(fn($products) => $this->output->progressStart($products->count()))
            ->each(function ($product) {
                $product->track();

                $this->output->progressAdvance();
            });

        $this->showResults();
    }

    protected function showResults(): void
    {
        $this->output->progressFinish();

        $data = Product::leftJoin('stock', 'stock.product_id', '=', 'products.id')
            ->get(['name', 'price', 'url', 'in_stock']);

        $this->table(
            ['Name', 'Price', 'Url', 'In Stock'],
            $data
        );
    }
}
