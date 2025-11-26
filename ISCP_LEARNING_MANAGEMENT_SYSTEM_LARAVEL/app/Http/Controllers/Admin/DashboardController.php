<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Statistik utama
        $totalGuru = DB::table('users')->count();
        $totalSiswa = DB::table('students')->count();
        $totalMapel = DB::table('mapels')->count();
        $totalProduk = DB::table('products')->count();
        $totalKelas = DB::table('classrooms')->count();
        $totalSerial = DB::table('serials')->count();
        $totalMateri = DB::table('lessons')->count();

        // Distribusi Materi per Mapel (untuk chart donat)
        $materiPerMapel = DB::table('lessons')
            ->join('mapels', 'lessons.mapel_id', '=', 'mapels.id')
            ->select('mapels.name as mapel', DB::raw('COUNT(lessons.id) as total'))
            ->groupBy('mapels.name')
            ->orderByDesc('total')
            ->get();

        // Jumlah Siswa per Kelas (untuk chart donat)
        $siswaPerKelas = DB::table('students')
            ->join('classrooms', 'students.classroom_id', '=', 'classrooms.id')
            ->select('classrooms.name as kelas', DB::raw('COUNT(students.id) as total'))
            ->groupBy('classrooms.name')
            ->orderByDesc('total')
            ->get();

        return view('admin.dashboard', compact(
            'totalGuru',
            'totalSiswa',
            'totalMapel',
            'totalProduk',
            'totalKelas',
            'totalSerial',
            'totalMateri',
            'materiPerMapel',
            'siswaPerKelas'
        ));
    }

    // keep rest of resource methods if needed
    public function create()
    { /* ... */
    }
    public function store(Request $request)
    { /* ... */
    }
    public function show(string $id)
    { /* ... */
    }
    public function edit(string $id)
    { /* ... */
    }
    public function update(Request $request, string $id)
    { /* ... */
    }
    public function destroy(string $id)
    { /* ... */
    }
}
