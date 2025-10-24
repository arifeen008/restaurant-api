<?php
// app/Models/Category.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description', 'image_url'];

    // ความสัมพันธ์: Category หนึ่งหมวด มี MenuItem ได้หลายรายการ
    public function menuItems()
    {
        return $this->hasMany(MenuItem::class);
    }
}
