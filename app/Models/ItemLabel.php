<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemLabel extends Model
{
    use HasFactory;

    protected $fillable = ['item_id', 'order_id', 'qr_code', 'number'];
    public function item()
    {
        return $this->belongsTo(Items::class, 'item_id');
    }
}
