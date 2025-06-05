<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockStream extends Model
{
    protected $table = "stock_streams";
    protected $fillable = [
        "recorded_at",
        "recorded_date",
        "stock_price"
    ];
}
