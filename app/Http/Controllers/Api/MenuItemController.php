<?php

// app/Http/Controllers/Api/MenuItemController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MenuItemController extends Controller
{
    /**
     * แสดงรายการเมนูทั้งหมด
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // โหลดหมวดหมู่มาด้วย
        $menuItems = MenuItem::with('category')->where('is_available', true)->get();
        return response()->json($menuItems);
    }

    /**
     * สร้างเมนูใหม่
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'is_available' => 'boolean',
            // 'image_url' => 'nullable|url' (ถ้ามีการอัปโหลดรูป จะซับซ้อนกว่านี้)
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $menuItem = MenuItem::create($request->all());
        return response()->json($menuItem->load('category'), 201);
    }

    /**
     * แสดงเมนูเดียว
     * @param \App\Models\MenuItem $menuItem
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(MenuItem $menuItem)
    {
        return response()->json($menuItem->load('category'));
    }

    /**
     * อัปเดตเมนู
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\MenuItem $menuItem
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, MenuItem $menuItem)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|numeric|min:0',
            'category_id' => 'sometimes|required|exists:categories,id',
            'description' => 'nullable|string',
            'is_available' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $menuItem->update($request->all());
        return response()->json($menuItem->load('category'));
    }

    /**
     * ลบเมนู
     * @param \App\Models\MenuItem $menuItem
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(MenuItem $menuItem)
    {
        $menuItem->delete();
        return response()->json(null, 204);
    }
}
