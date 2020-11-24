<?php


namespace App\UseCases;

use App\Clients\StockStatus;
use App\Models\History;
use App\Models\Stock;
use App\Models\User;
use App\Notifications\ImportantStockUpdate;
use Illuminate\Foundation\Bus\Dispatchable;

class TrackStock
{
    use Dispatchable;

    private Stock $stock;

    protected StockStatus $status;

    public function __construct(Stock $stock)
    {
        $this->stock = $stock;
    }

    public function handle(): void
    {
        $this->checkAvailability();
        $this->notifyUser();
        $this->refreshStock();
        $this->recordToHistory();
    }

    protected function checkAvailability(): void
    {
        $this->status = $this->stock
            ->retailer
            ->client()
            ->checkAvailability($this->stock);
    }

    protected function notifyUser(): void
    {
        if ($this->isNowInStock()) {
            User::first()->notify(
                new ImportantStockUpdate($this->stock)
            );
        }
    }

    protected function refreshStock(): void
    {
        $this->stock->update([
            'in_stock' => $this->status->available,
            'price' => $this->status->price,
        ]);
    }

    protected function recordToHistory(): void
    {
        History::create([
            'price' => $this->stock->price,
            'in_stock' => $this->stock->in_stock,
            'product_id' => $this->stock->product_id,
            'stock_id' => $this->stock->id,
        ]);
    }

    protected function isNowInStock(): bool
    {
        return !$this->stock->in_stock && $this->status->available;
    }
}
