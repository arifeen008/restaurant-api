<?php

// app/Http/Controllers/Api/KitchenController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KitchenController extends Controller
{
    /**
     * ดึงรายการอาหารที่ต้องทำทั้งหมด (Pending หรือ Cooking)
     * @return \Illuminate\Http\JsonResponse
     */
    public function getActiveOrderItems()
    {
        // ดึงรายการอาหาร (OrderItem) ที่ยังไม่เสร็จ
        // โดยจัดกลุ่มตามออเดอร์ และรวมข้อมูลโต๊ะไปด้วย
        $orders = Order::with(['items' => function ($query) {
                // เอาเฉพาะรายการที่ยังไม่เสร็จ
                $query->whereIn('order_items.status', ['pending', 'cooking'])
                      ->with('menuItem');
            }, 'table'])
            ->whereIn('status', ['pending', 'cooking']) // เอาเฉพาะออเดอร์ที่ยังไม่เสร็จ
            ->orderBy('created_at', 'asc') // ออเดอร์เก่าขึ้นก่อน
            ->get();

        // กรองออเดอร์ที่ไม่มีรายการคงค้างออก
        $activeOrders = $orders->filter(function ($order) {
            return $order->items->isNotEmpty();
        });

        return response()->json($activeOrders->values());
    }

    /**
     * อัปเดตสถานะรายการอาหาร (เช่น กุ๊กกดว่ากำลังทำ)
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\OrderItem $orderItem
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateItemStatus(Request $request, OrderItem $orderItem)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,cooking,ready,served',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $orderItem->update(['status' => $request->status]);

        // (ยิง Event บอกพนักงานเสิร์ฟว่าอาหารเสร็จแล้ว 'ready')
        // if ($request->status === 'ready') {
        //    event(new ItemIsReady($orderItem));
        // }

        // (เช็คว่าทุกรายการในออเดอร์เสร็จหมดแล้วหรือยัง)
        $this->checkAndUpdateOrderStatus($orderItem->order);

        return response()->json($orderItem);
    }

    /**
     * (Helper) ตรวจสอบสถานะออเดอร์หลัก
     */
    protected function checkAndUpdateOrderStatus(Order $order)
    {
        $itemsStatuses = $order->items()->pluck('status');

        $allReady = $itemsStatuses->every(fn($status) => $status === 'ready');
        $allServed = $itemsStatuses->every(fn($status) => $status === 'served');

        if ($allServed) {
            $order->update(['status' => 'served']);
        } elseif ($allReady) {
            $order->update(['status' => 'ready']); // ทุกอย่างพร้อมเสิร์ฟ
        } else {
             // ถ้ามีบางอย่างยังทำอยู่
            if ($itemsStatuses->contains('cooking') || $itemsStatuses->contains('ready') || $itemsStatuses->contains('served')) {
                 $order->update(['status' => 'cooking']);
            }
        }
    }
}
