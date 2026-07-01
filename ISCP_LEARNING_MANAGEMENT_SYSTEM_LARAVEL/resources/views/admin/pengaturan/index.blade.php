@extends('admin.layouts.app')

@section('title', 'Pengaturan ')
@section('page_title', 'Pengaturan ')

@section('content')

    @php
        // ROLE USER SEKARANG
        $role = $userRole ?? (Auth::user()->role ?? 0);

        // Helper kecil untuk disabled
        if (!function_exists('btnDisable')) {
            function btnDisable($role, $allowed)
            {
                $isDisabled = !in_array($role, $allowed);
                return [
                    'class' => $isDisabled ? 'disabled' : '',
                    'style' => $isDisabled ? 'pointer-events:none; opacity:0.5;' : '',
                ];
            }
        }
    @endphp


    <div class="row g-3">

        {{-- ================= PROFIL AKUN (SEMUA ROLE BOLEH) ================= --}}
        <div class="col-md-12">
            <div class="card text-center h-200">
                <div class="card-body">
                    <i class="fas fa-user-circle fa-2x mb-2 "></i>
                    <h5 class="card-title">Profil Akun</h5>
                    <p class="card-text">Lihat dan perbarui informasi akun Anda</p>
                    <a href="{{ route('admin.profil.index') }}" class="btn btn-add w-100">Pilih</a>
                </div>
            </div>
        </div>

        @php $btn = btnDisable($role, [1]); @endphp
        <div class="col-md-4">
            <div class="card text-center h-200">
                <div class="card-body">
                    <i class="fas fa-users-cog fa-2x mb-2"></i>
                    <h5 class="card-title">Manajemen Admin</h5>
                    <p class="card-text">Kelola akun admin & role lainnya</p>
                    <a href="{{ route('admin.admin.index') }}" class="btn btn-add w-100 {{ $btn['class'] }}"
                        style="{{ $btn['style'] }}">
                        Pilih
                    </a>
                </div>
            </div>
        </div>

        @php $btn = btnDisable($role, [1,2, 3]); @endphp
        <div class="col-md-4">
            <div class="card text-center h-200">
                <div class="card-body">
                    <i class="bi bi-people-fill fa-2x mb-2"></i>
                    <h5 class="card-title">Manajemen Kelas</h5>
                    <p class="card-text">Kelola data kelas dan siswa</p>
                    <a href="{{ route('admin.kelas.index') }}" class="btn btn-add w-100 {{ $btn['class'] }}"
                        style="{{ $btn['style'] }}">
                        Pilih
                    </a>
                </div>
            </div>
        </div>

        @php $btn = btnDisable($role, [1,2, 3]); @endphp
        <div class="col-md-4">
            <div class="card text-center h-200">
                <div class="card-body">
                    <i class="fa-solid fa-children fa-2x mb-2"></i>
                    <h5 class="card-title">Manajemen Siswa</h5>
                    <p class="card-text">Kelola data akun siswa</p>
                    <a href="{{ route('admin.siswa.index') }}" class="btn btn-add w-100 {{ $btn['class'] }}"
                        style="{{ $btn['style'] }}">
                        Pilih
                    </a>
                </div>
            </div>
        </div>

        @php $btn = btnDisable($role, [1,2, 4]); @endphp
        <div class="col-md-4">
            <div class="card text-center h-200">
                <div class="card-body">
                    <i class="fas fa-brain fa-2x mb-2 "></i>
                    <h5 class="card-title">Tipe soal</h5>
                    <p class="card-text">Kelola Tipe soal</p>
                    <a href="{{ route('admin.pra-soal.tipe.index') }}" class="btn btn-add w-100 {{ $btn['class'] }}"
                        style="{{ $btn['style'] }}">
                        Pilih
                    </a>
                </div>
            </div>
        </div>

        @php $btn = btnDisable($role, [1, 2,4]); @endphp
        <div class="col-md-4">
            <div class="card text-center h-200">
                <div class="card-body">
                    <i class="fas fa-shapes fa-2x mb-2"></i>
                    <h5 class="card-title">Model soal</h5>
                    <p class="card-text">Kelola model soal</p>
                    <a href="{{ route('admin.pra-soal.model.index') }}" class="btn btn-add w-100 {{ $btn['class'] }}"
                        style="{{ $btn['style'] }}">
                        Pilih
                    </a>
                </div>
            </div>
        </div>

        @php $btn = btnDisable($role, [1,2, 5]); @endphp
        <div class="col-md-4">
            <div class="card text-center h-200">
                <div class="card-body">
                    <i class="fas fa-list-alt fa-2x mb-2"></i>
                    <h5 class="card-title">Kategori Pengaduan</h5>
                    <p class="card-text">Kelola daftar kategori pengaduan</p>
                    <a href="{{ route('admin.kategori-pertanyaan.index') }}" class="btn btn-add w-100 {{ $btn['class'] }}"
                        style="{{ $btn['style'] }}">
                        Pilih
                    </a>
                </div>
            </div>
        </div>

    </div>

@endsection
