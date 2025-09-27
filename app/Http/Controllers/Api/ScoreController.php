<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\ScoreResource;
use App\Models\Score;
use App\Models\Student;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ScoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $user = Auth::user();
            $studentIds = Student::where('class_id', $user->class_id)->pluck('id');

            // Ambil nilai untuk siswa-siswa tersebut
            $scores = Score::with(['student', 'criteria', 'period'])
                ->whereIn('student_id', $studentIds)
                ->get();

            return ApiResponse::success(
                ScoreResource::collection($scores),
                'Data nilai berhasil dimuat.'
            );
        } catch (\Exception $e) {
            return ApiResponse::error('Gagal memuat data nilai: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();

            $teacherClassId = $user->class_id;

            // Validasi input
            $validated = $request->validate([
                'student_id' => [
                    'required',
                    Rule::exists('students', 'id')->where(function ($query) use ($teacherClassId) {
                        $query->where('class_id', $teacherClassId);
                    }),
                ],
                'period_id' => 'required|exists:periods,id',
                'scores' => 'required|array',
                'scores.*' => 'required|numeric|min:0|max:100',
            ]);

            // Cek apakah nilai untuk siswa dan periode ini sudah ada
            $alreadyExists = Score::where('student_id', $validated['student_id'])
                ->where('period_id', $validated['period_id'])
                ->exists();

            if ($alreadyExists) {
                return ApiResponse::error('Siswa ini sudah memiliki nilai pada periode tersebut.', 422);
            }

            // Simpan setiap skor untuk kriteria
            $scores = [];
            foreach ($validated['scores'] as $criteriaId => $value) {
                $scores[] = Score::create([
                    'student_id' => $validated['student_id'],
                    'criteria_id' => $criteriaId,
                    'period_id' => $validated['period_id'],
                    'value' => $value,
                ]);
            }

            return ApiResponse::success(
                ScoreResource::collection($scores),
                'Nilai berhasil disimpan.',
                201
            );
        } catch (\Exception $e) {
            return ApiResponse::error('Gagal menyimpan nilai: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($studentId)
    {
        try {
            $user = Auth::user();

            // Ambil nilai, hanya jika siswa dari kelas guru tsb
            $scores = Score::with(['criteria', 'period', 'student'])
                ->where('student_id', $studentId)
                ->whereHas('student', function ($query) use ($user) {
                    $query->where('class_id', $user->class_id);
                })
                ->get();

            // Jika tidak ada data ditemukan
            if ($scores->isEmpty()) {
                throw new ModelNotFoundException('Data tidak ditemukan atau akses ditolak.');
            }

            // Ambil data dari skor pertama (karena siswa & period sama)
            $student = $scores->first()->student;
            $period = $scores->first()->period;

            // Format respons scores
            return ApiResponse::success([
                'student' => $student->name,
                'period' => $period->name,
                'scores' => $scores->map(function ($score) {
                    return [
                        'criteria' => $score->criteria->name,
                        'value' => $score->value,
                    ];
                })->values(),
            ], 'Data nilai berhasil dimuat');
        } catch (\Exception $e) {
            return ApiResponse::handleException($e);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Score $score)
    {
        try {
            $user = Auth::user();
            $teacherClassId = $user->class_id;

            // Validasi request
            $validated = $request->validate([
                'student_id' => [
                    'required',
                    Rule::exists('students', 'id')->where(function ($query) use ($teacherClassId) {
                        $query->where('class_id', $teacherClassId);
                    }),
                ],
                'period_id' => 'required|exists:periods,id',
                'scores' => 'required|array',
                'scores.*' => 'required|numeric|min:0|max:100',
            ]);

            // Cek apakah sudah ada nilai lain untuk student & period, selain nilai yang sedang diupdate
            $existing = Score::where('student_id', $validated['student_id'])
                ->where('period_id', $validated['period_id'])
                ->where('id', '!=', $score->id)
                ->exists();

            if ($existing) {
                return ApiResponse::error('Siswa ini sudah memiliki nilai pada periode yang sama.', 422);
            }

            // Update nilai per criteria menggunakan updateOrCreate
            $updatedScores = [];
            foreach ($validated['scores'] as $criteriaId => $value) {
                $updated = Score::updateOrCreate(
                    [
                        'student_id' => $validated['student_id'],
                        'criteria_id' => $criteriaId,
                        'period_id' => $validated['period_id'],
                    ],
                    [
                        'value' => $value,
                    ]
                );

                $updatedScores[] = $updated;
            }

            return ApiResponse::success(
                ScoreResource::collection($updatedScores),
                'Nilai berhasil diperbarui.'
            );
        } catch (\Exception $e) {
            return ApiResponse::error('Gagal memperbarui nilai: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Score $score)
    {
        try {
            $user = Auth::user();

            // Pastikan hanya wali kelas dari siswa terkait yang bisa hapus
            if ($score->student->class_id !== $user->class_id) {
                return ApiResponse::error('Anda tidak memiliki akses.', 403);
            }

            $score->delete();

            return ApiResponse::success(null, 'Nilai berhasil dihapus');
        } catch (\Exception $e) {
            return ApiResponse::error('Gagal menghapus nilai: ' . $e->getMessage(), 500);
        }
    }
}
