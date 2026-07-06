<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockPrice extends Model
{
    protected $fillable = [
        'stock_upload_id',
        'stock_name',
        'price',
        'date',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:6',
            'date' => 'date',
        ];
    }

    public function stockUpload(): BelongsTo
    {
        return $this->belongsTo(StockUpload::class);
    }
}
