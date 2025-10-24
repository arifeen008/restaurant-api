<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\MenuItemController;
use App\Http\Controllers\Api\TableController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\KitchenController;
use App\Http\Controllers\Api\ReportController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// (ในอนาคต ทุก Route ควรอยู่ใน Route::middleware('auth:sanctum')->group(...) )

// --- การจัดการเมนูและหมวดหมู่ (สำหรับ Admin) ---
Route::apiResource('categories', CategoryController::class);
Route::apiResource('menu-items', MenuItemController::class);

// --- การจัดการโต๊ะ (สำหรับ Admin/Manager) ---
Route::apiResource('tables', TableController::class);

// --- การจัดการออเดอร์ (สำหรับ พนักงานเสิร์ฟ) ---
Route::apiResource('orders', OrderController::class)->except(['update']); // เราจะใช้ Route อื่นในการอัปเดต

// Route สำหรับการ "เพิ่ม" รายการอาหาร (ถ้าโต๊ะมีออเดอร์อยู่แล้ว)
// (จริงๆ แล้ว POST /api/orders ใน Controller จะจัดการให้เอง)

// Route สำหรับการ "เช็คบิล/จ่ายเงิน"
Route::post('orders/{order}/checkout', [OrderController::class, 'checkout']);

// Route สำหรับ "ยกเลิก" ออเดอร์
Route::post('orders/{order}/cancel', [OrderController::class, 'cancel']);

// Route สำหรับ "ลบ" รายการอาหารออกจากออเดอร์
Route::delete('order-items/{item}', [OrderController::class, 'removeOrderItem']);


// --- สำหรับหน้าจอในครัว (Kitchen View) ---
Route::prefix('kitchen')->group(function () {
    // ดึงรายการอาหารที่ต้องทำทั้งหมด
    Route::get('active-items', [KitchenController::class, 'getActiveOrderItems']);

    // อัปเดตสถานะอาหาร (เช่น กำลังทำ, เสร็จแล้ว)
    Route::patch('order-items/{orderItem}/status', [KitchenController::class, 'updateItemStatus']);
});

// --- สำหรับหน้ารายงาน (Reports) ---
Route::prefix('reports')->group(function () {
    // รายงานสรุปยอดขาย (ตามช่วงเวลา)
    Route::get('summary', [ReportController::class, 'getSalesSummary']);
    // รายงาน 5 อันดับเมนูขายดี (ตามช่วงเวลา)
    Route::get('top-items', [ReportController::class, 'getTopSellingItems']);
});
