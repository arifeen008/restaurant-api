<?php

// app/Http/Controllers/Api/OrderController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
use App\Models\Table;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * แสดงออเดอร์ทั้งหมด
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // แสดงออเดอร์ที่ยังไม่จ่ายเงิน โดยเรียงจากใหม่ไปเก่า
        $orders = Order::with('table', 'user', 'items.menuItem')
            ->whereNotIn('status', ['paid', 'cancelled'])
            ->latest()
            ->get();
        return response()->json($orders);
    }

    /**
     * สร้างออเดอร์ใหม่ (เปิดโต๊ะ)
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'table_id' => 'required|exists:tables,id',
            //'user_id' => 'required|exists:users,id', // ปกติควรดึงจาก Auth
            'items' => 'required|array|min:1',
            'items.*.menu_item_id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $table = Table::find($request->table_id);
        if ($table->status === 'occupied') {
            // ถ้าโต๊ะไม่ว่าง ให้ไปใช้ฟังก์ชัน "เพิ่มรายการ" แทน
            $currentOrder = $table->currentOrder;
            if ($currentOrder) {
                return $this->addItemsToOrder($request, $currentOrder);
            }
        }

        // --- สร้างออเดอร์ใหม่ (Transaction) ---
        try {
            DB::beginTransaction();

            // สมมติว่า user_id = 1 (ต้องแก้เป็น Auth::id() ทีหลัง)
            $userId = 1; // $request->user()->id;

            $order = Order::create([
                'table_id' => $request->table_id,
                'user_id' => $userId,
                'status' => 'pending', // สถานะแรกคือ รอดำเนินการ
            ]);

            $totalAmount = 0;

            foreach ($request->items as $item) {
                $menuItem = MenuItem::find($item['menu_item_id']);
                if (!$menuItem || !$menuItem->is_available) {
                    throw new \Exception("เมนู " . ($menuItem->name ?? 'ID ' . $item['menu_item_id']) . " ไม่พร้อมให้บริการ");
                }

                $price = $menuItem->price;
                $totalAmount += ($price * $item['quantity']);

                $order->items()->create([
                    'menu_item_id' => $item['menu_item_id'],
                    'quantity' => $item['quantity'],
                    'price' => $price,
                    'notes' => $item['notes'] ?? null,
                    'status' => 'pending', // ส่งไปครัว
                ]);
            }

            // อัปเดตยอดรวม
            $order->total_amount = $totalAmount;
            $order->net_amount = $totalAmount;
            $order->save();

            // อัปเดตสถานะโต๊ะ
            $table->update(['status' => 'occupied']);

            DB::commit();

            // (จุดนี้คือจุดที่ต้องยิง Event ไปที่ครัว)
            // event(new NewOrderCreated($order));

            return response()->json($order->load('items.menuItem'), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()], 500);
        }
    }

    /**
     * แสดงออเดอร์เดียว
     * @param \App\Models\Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Order $order)
    {
        return response()->json($order->load('table', 'user', 'items.menuItem'));
    }

    /**
     * ฟังก์ชันภายใน: เพิ่มรายการอาหารในออเดอร์ที่มีอยู่
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    protected function addItemsToOrder(Request $request, Order $order)
    {
        $validator = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.menu_item_id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            DB::beginTransaction();
            $totalAmount = $order->total_amount;

            foreach ($request->items as $item) {
                $menuItem = MenuItem::find($item['menu_item_id']);
                if (!$menuItem || !$menuItem->is_available) {
                    throw new \Exception("เมนู " . ($menuItem->name ?? 'ID ' . $item['menu_item_id']) . " ไม่พร้อมให้บริการ");
                }

                $price = $menuItem->price;
                $totalAmount += ($price * $item['quantity']);

                // (ควรเช็คว่ามีรายการนี้อยู่แล้วหรือไม่ ถ้ามีให้อัปเดต quantity แทน)

                $order->items()->create([
                    'menu_item_id' => $item['menu_item_id'],
                    'quantity' => $item['quantity'],
                    'price' => $price,
                    'notes' => $item['notes'] ?? null,
                    'status' => 'pending', // ส่งไปครัว
                ]);
            }

            // อัปเดตยอดรวม (ยังไม่คิดส่วนลด)
            $order->total_amount = $totalAmount;
            $order->net_amount = $totalAmount;
            $order->status = 'pending'; // เปลี่ยนสถานะกลับเป็น pending (ถ้าจ่ายเงินแล้วมาสั่งเพิ่ม)
            $order->save();

            DB::commit();

            // (ยิง Event ไปครัว ว่ามีรายการเพิ่ม)
            // event(new OrderItemsAdded($order, $newItems));

            return response()->json($order->load('items.menuItem'), 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()], 500);
        }
    }


    /**
     * ฟังก์ชันเช็คบิล (ปิดโต๊ะ)
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkout(Request $request, Order $order)
    {
        if ($order->status === 'paid') {
            return response()->json(['message' => 'ออเดอร์นี้ชำระเงินแล้ว'], 400);
        }

        $validator = Validator::make($request->all(), [
            'discount' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:cash,credit_card,qr_promptpay,bank_transfer', // <-- เพิ่ม
            'transaction_ref' => 'nullable|string', // <-- เพิ่ม
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            DB::beginTransaction(); // <-- ใช้ Transaction

            $discount = $request->input('discount', 0);
            $netAmount = $order->total_amount - $discount;

            $order->update([
                'discount' => $discount,
                'net_amount' => $netAmount,
                'status' => 'paid', // เปลี่ยนสถานะเป็นจ่ายเงินแล้ว
            ]);

            // *** สร้างรายการชำระเงิน ***
            Payment::create([
                'order_id' => $order->id,
                'amount' => $netAmount,
                'payment_method' => $request->payment_method,
                'transaction_ref' => $request->transaction_ref,
                'status' => 'completed',
            ]);
            // *** สิ้นสุดส่วนที่เพิ่ม ***

            // คืนสถานะโต๊ะเป็น "available"
            $order->table()->update(['status' => 'available']);

            DB::commit(); // <-- ยืนยัน Transaction

            return response()->json($order);
        } catch (\Exception $e) {
            DB::rollBack(); // <-- ยกเลิก Transaction หากมีปัญหา
            return response()->json(['message' => 'เกิดข้อผิดพลาดในการเช็คบิล: ' . $e->getMessage()], 500);
        }
    }

    /**
     * ยกเลิกออเดอร์
     * @param \App\Models\Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancel(Order $order)
    {
        if (in_array($order->status, ['paid', 'cancelled'])) {
            return response()->json(['message' => 'ไม่สามารถยกเลิกออเดอร์ที่จ่ายเงินหรือยกเลิกไปแล้วได้'], 400);
        }

        $order->update(['status' => 'cancelled']);

        // คืนสถานะโต๊ะ
        $order->table()->update(['status' => 'available']);

        // (ควรยิง Event ไปครัวเพื่อบอกว่ายกเลิก)

        return response()->json(['message' => 'ออเดอร์ถูกยกเลิกแล้ว']);
    }

    /**
     * (ฟังก์ชันนี้ไม่ได้ใช้ใน apiResource แต่มีประโยชน์)
     * ลบรายการอาหารออกจากออเดอร์
     * @param \App\Models\OrderItem $item
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeOrderItem(OrderItem $item)
    {
        // (ต้องเช็คสิทธิ์ก่อนว่าใครลบได้)
        // (ต้องเช็คว่าสถานะรายการอาหารยังเป็น pending)
        if ($item->status !== 'pending') {
            return response()->json(['message' => 'ไม่สามารถลบรายการที่กำลังทำหรือเสิร์ฟแล้วได้'], 400);
        }

        try {
            DB::beginTransaction();
            $order = $item->order;
            $removedAmount = $item->price * $item->quantity;

            $item->delete();

            // อัปเดตยอดรวมใน Order หลัก
            $order->total_amount = $order->total_amount - $removedAmount;
            $order->net_amount = $order->total_amount - $order->discount; // คำนวณใหม่
            $order->save();

            DB::commit();
            return response()->json(null, 204);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()], 500);
        }
    }
}
