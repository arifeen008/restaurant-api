<?php

// app/Models/Order.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'table_id',
        'user_id',
        'total_amount',
        'discount',
        'net_amount',
        'status',
        'notes',
    ];

    // ความสัมพันธ์: Order หนึ่งใบ อยู่ที่ Table เดียว
    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    // ความสัมพันธ์: Order หนึ่งใบ มีพนักงานรับ (User) คนเดียว
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ความสัมพันธ์: Order หนึ่งใบ มี OrderItem ได้หลายรายการ
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
