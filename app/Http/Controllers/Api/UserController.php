<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $users = User::all();
            return UserResource::collection($users);
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
                'name'      => 'required|string|max:255',
                'email'     => 'required|email|unique:users,email',
                'password'  => 'required|string|min:6',
                'role'      => 'required|exists:roles,name',
                'class_id'  => 'nullable|exists:class_rooms,id',
            ]);

            $user = User::create([
                'name'              => $validated['name'],
                'email'             => $validated['email'],
                'password'          => Hash::make($validated['password']),
                'class_id'          => $validated['class_id'] ?? null,
                'email_verified_at' => now(),
            ]);

            $user->assignRole($validated['role']);

            return ApiResponse::success(new UserResource($validated), 'Pengguna berhasil ditambahkan', 201);
        } catch (\Exception $e) {
            return ApiResponse::error('Gagal menambahkan pengguna: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        try {
            return ApiResponse::success(new UserResource($user));
        } catch (\Exception $e) {
            return ApiResponse::error('Gagal menampilkan pengguna: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        try {
            $validated = $request->validate([
                'name'      => 'sometimes|string|max:255',
                'email'     => ['sometimes', 'email', Rule::unique('users')->ignore($user->id)],
                'password'  => 'sometimes|string|min:6',
                'role'      => 'sometimes|exists:roles,name',
                'class_id'  => 'nullable|exists:class_rooms,id',
            ]);

            if (isset($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            }

            $user->update($validated);

            if (isset($validated['role'])) {
                $user->syncRoles([$validated['role']]);
            }

            return ApiResponse::success(new UserResource($user), 'Data pengguna berhasil diperbarui');
        } catch (\Exception $e) {
            return ApiResponse::error('Gagal memperbarui pengguna: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            $user->delete();
            return ApiResponse::success(null, 'Pengguna berhasil dihapus');
        } catch (\Exception $e) {
            return ApiResponse::error('Gagal menghapus pengguna: ' . $e->getMessage(), 500);
        }
    }
}
