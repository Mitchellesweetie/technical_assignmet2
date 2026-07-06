<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockUpload extends Model
{
    protected $fillable = [
        'user_id',
        'filename',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function stockPrices(): HasMany
    {
        return $this->hasMany(StockPrice::class);
    }
}
