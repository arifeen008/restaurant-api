<?php

// app/Models/MenuItem.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'price',
        'category_id',
        'image_url',
        'is_available',
    ];

    // ความสัมพันธ์: MenuItem หนึ่งรายการ อยู่ใน Category เดียว
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // ความสัมพันธ์: MenuItem หนึ่งรายการ อยู่ใน OrderItem ได้หลายครั้ง
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
