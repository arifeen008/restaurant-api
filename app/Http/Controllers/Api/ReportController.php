<?php

// app/Http/Controllers/Api/ReportController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{
    /**
     * ดึงข้อมูลสรุปยอดขาย (ยอดรวม, จำนวนออเดอร์) และยอดขายรายวัน
     */
    public function getSalesSummary(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        // 1. สรุปยอดรวม
        $summary = Order::where('status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('SUM(net_amount) as total_sales'),
                DB::raw('COUNT(id) as total_orders'),
                DB::raw('AVG(net_amount) as average_order_value')
            )
            ->first();

        // 2. ยอดขายรายวัน (สำหรับทำกราฟ)
        $dailySales = Order::where('status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(net_amount) as total')
            )
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date', 'asc')
            ->get();

        // 3. สรุปตามวิธีการชำระเงิน
        $paymentMethods = DB::table('payments')
            ->join('orders', 'payments.order_id', '=', 'orders.id')
            ->where('orders.status', 'paid')
            ->whereBetween('payments.created_at', [$startDate, $endDate])
            ->select(
                'payments.payment_method',
                DB::raw('SUM(payments.amount) as total_amount'),
                DB::raw('COUNT(payments.id) as total_transactions')
            )
            ->groupBy('payments.payment_method')
            ->get();

        return response()->json([
            'summary' => $summary,
            'daily_sales' => $dailySales,
            'payment_methods' => $paymentMethods,
        ]);
    }

    /**
     * ดึง 5 อันดับเมนูขายดี
     */
    public function getTopSellingItems(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        $topItems = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
            ->where('orders.status', 'paid')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->select(
                'menu_items.name as item_name',
                DB::raw('SUM(order_items.quantity) as total_quantity_sold'),
                DB::raw('SUM(order_items.quantity * order_items.price) as total_revenue')
            )
            ->groupBy('menu_items.name')
            ->orderBy('total_quantity_sold', 'desc')
            ->limit(5)
            ->get();

        return response()->json($topItems);
    }
}
