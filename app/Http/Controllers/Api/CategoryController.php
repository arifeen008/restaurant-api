<?php

// app/Http/Controllers/Api/CategoryController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * แสดงรายการหมวดหมู่ทั้งหมด
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // /api/categories?with_items=true (เพื่อโหลดเมนูในหมวดหมู่มาด้วย)
        if ($request->has('with_items')) {
            $categories = Category::with('menuItems')->get();
        } else {
            $categories = Category::all();
        }

        return response()->json($categories);
    }

    /**
     * สร้างหมวดหมู่ใหม่
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422); // 422 Unprocessable Entity
        }

        $category = Category::create($request->all());
        return response()->json($category, 201); // 201 Created
    }

    /**
     * แสดงข้อมูลหมวดหมู่เดียว
     * @param \App\Models\Category $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Category $category)
    {
        // โหลดเมนูที่เกี่ยวข้องมาด้วย
        return response()->json($category->load('menuItems'));
    }

    /**
     * อัปเดตหมวดหมู่
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Category $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $category->update($request->all());
        return response()->json($category);
    }

    /**
     * ลบหมวดหมู่
     * @param \App\Models\Category $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Category $category)
    {
        // (ควรเพิ่ม Logic ตรวจสอบว่ามี Menu Item ใช้งานอยู่หรือไม่ก่อนลบ)
        $category->delete();
        return response()->json(null, 204); // 204 No Content
    }
}
