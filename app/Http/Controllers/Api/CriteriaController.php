<?php

namespace App\Http\Controllers\Api;

use App\Enums\PriorityEnum;
use App\Enums\TypeEnum;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\CriteriaResource;
use App\Models\Criteria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class CriteriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $criteria = Criteria::all();
            return CriteriaResource::collection($criteria);
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
                'name' => 'required|string|max:255|unique:criterias,name',
                'type' => ['required', new Enum(TypeEnum::class)],
                'p_threshold' => 'required|numeric',
                'priority_value' => ['required', new Enum(PriorityEnum::class)],
            ]);

            $criteria = Criteria::create($validated);

            return ApiResponse::success(
                new CriteriaResource($criteria),
                'Data kriteria berhasil ditambah.',
                201
            );
        } catch (\Exception $e) {
            Log::error('Error saat menambah data kriteria: ' . $e->getMessage());

            return ApiResponse::error(
                'Gagal menambah data kriteria.',
                $e->getMessage(),
                500
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Criteria $criteria)
    {
        return new CriteriaResource($criteria);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Criteria $criteria)
    {
        try {
            $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('criterias')->ignore($criteria->id),
                ],
                'type' => ['required', new Enum(TypeEnum::class)],
                'p_threshold' => 'required|numeric',
                'priority_value' => ['required', new Enum(PriorityEnum::class)],
            ]);

            $criteria->update($request->only('name'));

            return ApiResponse::success(
                new CriteriaResource($criteria),
                'Data kriteria berhasil diubah.',
                200
            );
        } catch (\Exception $e) {
            Log::error('Error saat mengubah data kriteria: ' . $e->getMessage());

            return ApiResponse::error(
                'Gagal mengubah data kriteria.',
                $e->getMessage(),
                500
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Criteria $criteria)
    {
        try {
            $criteria->delete();

            return ApiResponse::success(
                null,
                'Data periode berhasil dihapus.',
                200
            );
        } catch (\Exception $e) {
            Log::error('Error saat menghapus periode: ' . $e->getMessage());

            return ApiResponse::error(
                'Gagal menghapus data periode.',
                $e->getMessage(),
                500
            );
        }
    }
}
