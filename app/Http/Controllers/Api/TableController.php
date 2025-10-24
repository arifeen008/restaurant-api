<?php

// app/Http\Controllers\Api\TableController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TableController extends Controller
{
    /**
     * แสดงโต๊ะทั้งหมด
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // โหลดออเดอร์ปัจจุบันที่ยังไม่จ่ายเงินไปด้วย
        $tables = Table::with('currentOrder')->get();
        return response()->json($tables);
    }

    /**
     * สร้างโต๊ะใหม่
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:tables',
            'capacity' => 'required|integer|min:1',
            'status' => 'sometimes|in:available,occupied,reserved,cleaning',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $table = Table::create($request->all());
        return response()->json($table, 201);
    }

    /**
     * แสดงโต๊ะเดียว
     * @param \App\Models\Table $table
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Table $table)
    {
        return response()->json($table->load('currentOrder.items.menuItem'));
    }

    /**
     * อัปเดตโต๊ะ
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Table $table
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Table $table)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255|unique:tables,name,' . $table->id,
            'capacity' => 'sometimes|required|integer|min:1',
            'status' => 'sometimes|in:available,occupied,reserved,cleaning',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $table->update($request->all());
        return response()->json($table);
    }

    /**
     * ลบโต๊ะ
     * @param \App\Models\Table $table
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Table $table)
    {
        // (ควรเช็คว่าโต๊ะว่างหรือไม่ก่อนลบ)
        if ($table->status === 'occupied') {
            return response()->json(['message' => 'ไม่สามารถลบโต๊ะที่กำลังใช้งานได้'], 400);
        }
        $table->delete();
        return response()->json(null, 204);
    }
}
