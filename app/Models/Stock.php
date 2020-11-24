<?php

namespace App\Models;

use App\UseCases\TrackStock;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stock extends Model
{
    use HasFactory;

    protected $table = 'stock';

    protected $casts = [
        'in_stock' => 'boolean'
    ];

    public function track(): void
    {
        TrackStock::dispatch($this);
    }

    public function retailer(): BelongsTo
    {
        return $this->belongsTo(Retailer::class);
    }

    public function product(): belongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
