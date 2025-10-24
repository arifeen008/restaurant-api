<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // vvv เพิ่มส่วนนี้ทั้งหมด vvv
    protected $fillable = [
        'order_id',
        'amount',
        'payment_method',
        'transaction_ref',
        'status',
    ];
    // ^^^ สิ้นสุดส่วนที่เพิ่ม ^^^

    /**
     * Get the order that this payment belongs to.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
