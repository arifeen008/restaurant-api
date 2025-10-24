<?php

// app/Models/OrderItem.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id',
        'menu_item_id',
        'quantity',
        'price',
        'notes',
        'status',
    ];

    // ความสัมพันธ์: OrderItem หนึ่งรายการ อยู่ใน Order เดียว
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // ความสัมพันธ์: OrderItem หนึ่งรายการ คือ MenuItem เดียว
    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class);
    }
}
