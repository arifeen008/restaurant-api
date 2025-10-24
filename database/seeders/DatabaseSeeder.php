<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Table;
use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. สร้าง User หลัก (Admin)
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('thisisadmin'), // password คือ "password"
            'role' => 'admin',
        ]);

        // 2. สร้างพนักงานเสิร์ฟ
        User::factory(3)->create([
            'role' => 'waiter',
            'password' => Hash::make('thisisuser'),
        ]);

        // 3. สร้างโต๊ะ
        Table::factory(20)->create();

        // เราจะล้างข้อมูลเก่าก่อน (เผื่อรันซ้ำ)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;'); // ปิดการเช็ค FK ชั่วคราว
        Category::truncate();
        MenuItem::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;'); // เปิดการเช็ค FK

        // --- สร้างหมวดหมู่ ---

        $category1 = Category::create([
            'name' => 'อาหารจานเดียว',
            'description' => 'เมนูอิ่มอร่อยในจานเดียว'
        ]);

        $category2 = Category::create([
            'name' => 'ของทานเล่น',
            'description' => 'เมนูทานเล่นเพลินๆ'
        ]);

        $category3 = Category::create([
            'name' => 'ของหวาน',
            'description' => 'ตบท้ายมื้ออาหาร'
        ]);

        $category4 = Category::create([
            'name' => 'เครื่องดื่ม',
            'description' => 'เครื่องดื่มเย็นชื่นใจ'
        ]);

        // --- สร้างเมนูอาหาร (ผูกกับหมวดหมู่) ---

        // เมนูอาหารจานเดียว (ผูกกับ $category1)
        MenuItem::create([
            'category_id' => $category1->id,
            'name' => 'ข้าวกะเพราไก่ไข่ดาว',
            'description' => 'กะเพราไก่สับรสจัดจ้าน พร้อมไข่ดาว',
            'price' => 70,
            'is_available' => true,
        ]);
        MenuItem::create([
            'category_id' => $category1->id,
            'name' => 'ข้าวผัดกุ้ง',
            'description' => 'ข้าวผัดหอมกลิ่นกระทะ ใส่กุ้งสด',
            'price' => 80,
            'is_available' => true,
        ]);
        MenuItem::create([
            'category_id' => $category1->id,
            'name' => 'ผัดไทยกุ้งสด',
            'description' => 'ผัดไทยเส้นจันท์เหนียวนุ่ม กุ้งสดตัวโต',
            'price' => 85,
            'is_available' => true,
        ]);
        MenuItem::create([
            'category_id' => $category1->id,
            'name' => 'ข้าวไข่เจียวหมูสับ',
            'description' => 'ไข่เจียวฟูๆ ใส่หมูสับ',
            'price' => 60,
            'is_available' => true,
        ]);
        MenuItem::create([
            'category_id' => $category1->id,
            'name' => 'ข้าวขาหมู',
            'description' => 'ข้าวขาหมูเนื้อนุ่ม พร้อมผักกาดดอง',
            'price' => 75,
            'is_available' => true,
        ]);


        // เมนูของทานเล่น (ผูกกับ $category2)
        MenuItem::create([
            'category_id' => $category2->id,
            'name' => 'เฟรนช์ฟรายส์',
            'description' => 'มันฝรั่งทอดกรอบโรยเกลือ',
            'price' => 80,
            'is_available' => true,
        ]);
        MenuItem::create([
            'category_id' => $category2->id,
            'name' => 'ไก่ทอดซอสเกาหลี',
            'description' => 'ปีกไก่ทอดคลุกซอสเกาหลีรสเด็ด',
            'price' => 120,
            'is_available' => true,
        ]);
        MenuItem::create([
            'category_id' => $category2->id,
            'name' => 'นักเก็ตไก่',
            'description' => 'นักเก็ตไก่ 6 ชิ้น พร้อมซอสมะเขือเทศ',
            'price' => 75,
            'is_available' => true,
        ]);

        // เมนูของหวาน (ผูกกับ $category3)
        MenuItem::create([
            'category_id' => $category3->id,
            'name' => 'บิงซูสตรอว์เบอร์รี',
            'description' => 'น้ำแข็งไสเกล็ดหิมะ ราดซอสสตรอว์เบอร์รี',
            'price' => 150,
            'is_available' => true,
        ]);
        MenuItem::create([
            'category_id' => $category3->id,
            'name' => 'ฮันนี่โทสต์',
            'description' => 'ขนมปังอบเนย ราดน้ำผึ้ง เสิร์ฟพร้อมไอศกรีมวานิลลา',
            'price' => 135,
            'is_available' => true,
        ]);

        // เมนูเครื่องดื่ม (ผูกกับ $category4)
        MenuItem::create([
            'category_id' => $category4->id,
            'name' => 'ชาไทยเย็น',
            'description' => 'ชาไทยรสเข้มข้น หอมมัน',
            'price' => 55,
            'is_available' => true,
        ]);
        MenuItem::create([
            'category_id' => $category4->id,
            'name' => 'กาแฟลาเต้เย็น',
            'description' => 'กาแฟนมรสนุ่ม',
            'price' => 65,
            'is_available' => true,
        ]);
        MenuItem::create([
            'category_id' => $category4->id,
            'name' => 'น้ำส้มคั้นสด',
            'description' => 'น้ำส้มคั้นสด 100%',
            'price' => 70,
            'is_available' => true,
        ]);
        MenuItem::create([
            'category_id' => $category4->id,
            'name' => 'น้ำเปล่า',
            'description' => 'น้ำดื่มสะอาด',
            'price' => 20,
            'is_available' => true,
        ]);
    }
}
