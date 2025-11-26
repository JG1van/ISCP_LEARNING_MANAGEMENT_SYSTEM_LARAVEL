@extends('layouts.app')

@section('title', 'Pengaturan ')
@section('page_title', 'Pengaturan ')

@section('content')
    <div class="row g-3">

        {{-- Profil Akun --}}
        <div class="col-md-12">
            <div class="card text-center h-200">
                <div class="card-body">
                    <i class="fas fa-user-circle fa-2x mb-2 "></i>
                    <h5 class="card-title">Profil Akun</h5>
                    <p class="card-text">Lihat dan perbarui informasi akun Anda.</p>
                    <a href="{{ route('admin.profil.index') }}" class="btn btn-add w-100">Pilih</a>
                </div>
            </div>
        </div>

        {{-- Manajemen Pengguna --}}
        <div class="col-md-4">
            <div class="card text-center h-200">
                <div class="card-body">
                    <i class="fas fa-users-cog fa-2x mb-2"></i>
                    <h5 class="card-title">Manajemen Admin</h5>
                    <p class="card-text">Kelola akun admin & role lainnya.</p>
                    <a href="{{ route('admin.admin.index') }}" class="btn btn-add w-100">Pilih</a>
                </div>
            </div>
        </div>

        {{-- Manajemen Guru --}}
        <div class="col-md-4">
            <div class="card text-center h-200">
                <div class="card-body">
                    <i class="fas fa-chalkboard-teacher fa-2x mb-2 "></i>
                    <h5 class="card-title">Manajemen Guru</h5>
                    <p class="card-text">Kelola data guru, akses, dan status akun.</p>
                    <a href="{{ route('admin.guru.index') }}" class="btn btn-add w-100">Pilih</a>
                </div>
            </div>
        </div>

        {{-- Manajemen Siswa --}}
        <div class="col-md-4">
            <div class="card text-center h-200">
                <div class="card-body">
                    <i class="fa-solid fa-children fa-2x mb-2"></i>
                    <h5 class="card-title">Manajemen Siswa</h5>
                    <p class="card-text">Kelola data akun siswa dan statusnya.</p>
                    <a href="{{ route('admin.siswa.index') }}" class="btn btn-add w-100">Pilih</a>
                </div>
            </div>
        </div>
        {{-- Pengaturan Tipe Latihan --}}
        <div class="col-md-4">
            <div class="card text-center h-200">
                <div class="card-body">
                    <i class="fas fa-brain fa-2x mb-2 "></i>
                    <h5 class="card-title">Tipe Latihan</h5>
                    <p class="card-text">Kelola jenis latihan.</p>
                    <a href="{{ route('admin.pra_latihan.tipe.index') }}" class="btn btn-add w-100">Pilih</a>
                </div>
            </div>
        </div>

        {{-- Pengaturan Model Latihan --}}
        <div class="col-md-4">
            <div class="card text-center h-200">
                <div class="card-body">
                    <i class="fas fa-shapes fa-2x mb-2"></i>
                    <h5 class="card-title">Model Latihan</h5>
                    <p class="card-text">Kelola model latihan pembelajaran.</p>
                    <a href="{{ route('admin.pra_latihan.model.index') }}" class="btn btn-add w-100">Pilih</a>
                </div>
            </div>
        </div>
        {{-- Kategori Pengaduan --}}
        <div class="col-md-4">
            <div class="card text-center h-200">
                <div class="card-body">
                    <i class="fas fa-list-alt fa-2x mb-2"></i>
                    <h5 class="card-title">Kategori Pengaduan</h5>
                    <p class="card-text">Kelola daftar kategori pengaduan</p>
                    <a href="{{ route('admin.kategori_pengaduan.index') }}" class="btn btn-add w-100">Pilih</a>
                </div>
            </div>
        </div>
    @endsection
