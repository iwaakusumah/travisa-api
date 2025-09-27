<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\PeriodResource;
use App\Models\Period;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class PeriodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $periods = Period::all();
            return PeriodResource::collection($periods);
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
                'name' => 'required|string|max:255|unique:periods,name',
            ]);

            $period = Period::create($validated);

            return ApiResponse::success(
                new PeriodResource($period),
                'Data periode berhasil ditambah.',
                201
            );
        } catch (\Exception $e) {
            Log::error('Error saat menambah data periode: ' . $e->getMessage());

            return ApiResponse::error(
                'Gagal menambah data periode.',
                $e->getMessage(),
                500
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Period $period)
    {
        return new PeriodResource($period);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Period $period)
    {
        try {
            $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('periods')->ignore($period->id),
                ],
            ]);

            $period->update($request->only('name'));

            return ApiResponse::success(
                new PeriodResource($period),
                'Data periode berhasil diubah.',
                200
            );
        } catch (\Exception $e) {
            Log::error('Error saat mengubah data periode: ' . $e->getMessage());

            return ApiResponse::error(
                'Gagal mengubah data periode.',
                $e->getMessage(),
                500
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Period $period)
    {
        try {
            $period->delete();

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
