<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'user_id',
        'customer_id',
        'order_number',
        'total',
    ];

    public function items()
    {
        return $this->hasMany(OrderItems::class, 'order_id');
    }
}
