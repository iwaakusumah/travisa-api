<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\ClassRoomResource;
use App\Models\ClassRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ClassRoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $classes = ClassRoom::all();
            return ClassRoomResource::collection($classes);
        } catch (\Exception $e) {
            return ApiResponse::error('Gagal memuat data nlai: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:class_rooms,name',
            ]);

            $class = ClassRoom::create($validated);

            return ApiResponse::success(
                new ClassRoomResource($class),
                'Data kelas berhasil ditambah.',
                201
            );
        } catch (\Exception $e) {
            Log::error('Error saat menambah data kelas: ' . $e->getMessage());

            return ApiResponse::error(
                'Gagal menambah data kelas.',
                $e->getMessage(),
                500
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ClassRoom $class)
    {
        return new ClassRoomResource($class);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ClassRoom $class)
    {
        try {
            $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('class_rooms')->ignore($class->id),
                ],
            ]);

            $class->update($request->only('name'));

            return ApiResponse::success(
                new ClassRoomResource($class),
                'Data kelas berhasil diubah.',
                200
            );
        } catch (\Exception $e) {
            Log::error('Error saat mengubah data kelas: ' . $e->getMessage());

            return ApiResponse::error(
                'Gagal mengubah data kelas.',
                $e->getMessage(),
                500
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ClassRoom $class)
    {
        try {
            $class->delete();

            return ApiResponse::success(
                null,
                'Data kelas berhasil dihapus.',
                201
            );
        } catch (\Exception $e) {
            Log::error('Error saat menghapus kelas: ' . $e->getMessage());

            return ApiResponse::error(
                'Gagal menghapus data kelas.',
                $e->getMessage(),
                500
            );
        }
    }
}
