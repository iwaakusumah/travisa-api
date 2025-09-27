<?php

namespace App\Http\Controllers\Api;

use App\Enums\GenderEnum;
use App\Enums\LevelEnum;
use App\Enums\MajorEnum;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\StudentResource;
use App\Models\ClassRoom;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $user = Auth::user();

            // Ambil data siswa
            $students = $user->hasRole('administration')
                ? Student::with('class')->get()
                : Student::with('class')->where('class_id', $user->class_id)->get();

            // Jika bukan admin, pastikan class valid
            $class = $user->class_id ? ClassRoom::find($user->class_id) : null;
            if ($user->hasRole('teacher') && !$class) {
                return ApiResponse::error('Kelas tidak ditemukan.', 404);
            }
        } catch (\Exception $e) {
            return ApiResponse::error('Gagal memuat data siswa: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();

            if ($user->role === 'administration') {
                // Admin boleh pilih class_id
                $validated = $request->validate([
                    'name' => 'required|string|max:255|unique:students,name',
                    'gender' => ['required', new Enum(GenderEnum::class)],
                    'class_id' => 'required|exists:class_rooms,id',
                ]);

                $class = ClassRoom::findOrFail($validated['class_id']);
            } else {
                // Wali kelas: class_id diambil dari user, tidak boleh input
                $validated = $request->validate([
                    'name' => 'required|string|max:255|unique:students,name',
                    'gender' => ['required', new Enum(GenderEnum::class)],
                ]);

                if (!$user->class_id) {
                    return ApiResponse::error('Wali kelas belum memiliki kelas.', 400);
                }

                $class = ClassRoom::findOrFail($user->class_id);
                $validated['class_id'] = $class->id; // inject class_id ke validasi
            }

            $className = strtoupper($class->name); // Misal: "X TKJ 3"

            // Tentukan level
            $level = match (true) {
                str_starts_with($className, 'XII') => LevelEnum::XII,
                str_starts_with($className, 'XI') => LevelEnum::XI,
                str_starts_with($className, 'X') => LevelEnum::X,
                default => null,
            };

            // Tentukan major
            $major = match (true) {
                str_contains($className, 'TKJ') => MajorEnum::TKJ,
                str_contains($className, 'TKR') => MajorEnum::TKR,
                str_contains($className, 'AK') => MajorEnum::AK,
                str_contains($className, 'AP') => MajorEnum::AP,
                default => null,
            };

            if (!$level || !$major) {
                return ApiResponse::error('Tidak dapat menentukan jurusan atau level dari nama kelas.', 422);
            }

            $student = Student::create([
                'name' => $validated['name'],
                'gender' => $validated['gender'],
                'class_id' => $validated['class_id'],
                'level' => $level,
                'major' => $major,
            ]);

            return ApiResponse::success(new StudentResource($student), 'Siswa berhasil ditambahkan', 201);

        } catch (\Exception $e) {
            return ApiResponse::error('Gagal menambahkan siswa: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student)
    {
        try {
            return ApiResponse::success(new StudentResource($student));
        } catch (\Exception $e) {
            return ApiResponse::error('Gagal menampilkan siswa: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
    {
        try {
            $user = Auth::user();

            // Cek akses untuk homeroom teacher
            if ($user->role !== 'administration' && $student->class_id !== $user->class_id) {
                return ApiResponse::error('Anda tidak memiliki akses untuk mengedit siswa ini.', 403);
            }

            if ($user->role === 'administration') {
                // Admin bisa mengedit semua data, termasuk class_id
                $validated = $request->validate([
                    'name' => [
                        'required',
                        'string',
                        'max:255',
                        Rule::unique('students', 'name')->ignore($student->id),
                    ],
                    'gender' => ['required', new Enum(GenderEnum::class)],
                    'class_id' => 'required|exists:class_rooms,id',
                ]);

                $class = ClassRoom::findOrFail($validated['class_id']);
            } else {
                // Wali kelas hanya bisa mengedit name & gender, class_id diambil dari user
                $validated = $request->validate([
                    'name' => [
                        'required',
                        'string',
                        'max:255',
                        Rule::unique('students', 'name')->ignore($student->id),
                    ],
                    'gender' => ['required', new Enum(GenderEnum::class)],
                ]);

                $validated['class_id'] = $user->class_id;
                $class = ClassRoom::findOrFail($user->class_id);
            }

            // Dapatkan nama kelas dalam huruf besar
            $className = strtoupper($class->name);

            // Tentukan level
            $level = match (true) {
                str_starts_with($className, 'XII') => LevelEnum::XII,
                str_starts_with($className, 'XI') => LevelEnum::XI,
                str_starts_with($className, 'X') => LevelEnum::X,
                default => null,
            };

            // Tentukan major
            $major = match (true) {
                str_contains($className, 'TKJ') => MajorEnum::TKJ,
                str_contains($className, 'TKR') => MajorEnum::TKR,
                str_contains($className, 'AK') => MajorEnum::AK,
                str_contains($className, 'AP') => MajorEnum::AP,
                default => null,
            };

            if (!$level || !$major) {
                return ApiResponse::error('Tidak dapat menentukan jurusan atau level dari nama kelas.', 422);
            }

            $student->update([
                'name' => $validated['name'],
                'gender' => $validated['gender'],
                'class_id' => $validated['class_id'],
                'level' => $level,
                'major' => $major,
            ]);

            return ApiResponse::success(new StudentResource($student), 'Data siswa berhasil diperbarui');
        } catch (\Exception $e) {
            return ApiResponse::error('Gagal memperbarui siswa: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        // Validasi: Cek apakah siswa memiliki data nilai
        if ($student->nilai()->exists()) {
            return ApiResponse::error('Siswa tidak bisa dihapus karena masih memiliki data nilai.', 400);
        }

        try {
            $student->delete();
            return ApiResponse::success(null, 'Siswa berhasil dihapus');
        } catch (\Exception $e) {
            return ApiResponse::error('Gagal menghapus siswa: ' . $e->getMessage(), 500);
        }
    }
}
