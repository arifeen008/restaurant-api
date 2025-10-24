<?php

// app/Models/Table.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'capacity', 'status'];

    // ความสัมพันธ์: Table หนึ่งโต๊ะ มีได้หลาย Order
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // (Helper) ดึงออเดอร์ล่าสุดที่ยังไม่จ่ายเงิน
    public function currentOrder()
    {
        return $this->hasOne(Order::class)->whereNotIn('status', ['paid', 'cancelled'])->latest();
    }
}
