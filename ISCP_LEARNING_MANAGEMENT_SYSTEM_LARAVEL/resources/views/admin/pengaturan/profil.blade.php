@extends('layouts.app')

@section('title', 'Profil Admin')
@section('page_title', 'Profil Admin')

@section('content')
    <div class="container">
        @if (session('success'))
            <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger shadow-sm">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="card-body p-4">
                <form action="{{ route('admin.profil.update') }}" method="POST" enctype="multipart/form-data"
                    autocomplete="off">
                    @csrf
                    <div class="row g-4 align-items-start">


                        <div class="col-md-4 d-flex flex-column align-items-center">
                            <div class="bg-light rounded-4 shadow-sm p-4 d-flex flex-column align-items-center justify-content-center w-100"
                                style="min-height: auto;">
                                <div class="position-relative mb-3">
                                    <img id="imgPreview"
                                        src="{{ $admin->img ? asset('images/admins/' . $admin->img) : asset('images/logo.webp') }}"
                                        alt="Foto Admin" class="rounded-circle border shadow-sm bg-white" width="130"
                                        height="130" style="object-fit: cover;">
                                    <button type="button"
                                        class="btn btn-sm btn-add rounded-circle position-absolute bottom-0 end-0 translate-middle"
                                        id="btnChangePhoto" style="width: 35px; height: 35px;">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                </div>

                                {{-- data singkat di bawah foto --}}
                                <h5 class="fw-bold mb-1">{{ $admin->name }}</h5>
                                <p class="text-muted mb-1 small">{{ '@' . $admin->username }}</p>

                                <table class="table table-sm text-start w-auto mb-0">
                                    <tr>
                                        <th class="text-muted pe-2">Jabatan:</th>
                                        <td>{{ $admin->position ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted pe-2">Login Terakhir:</th>
                                        <td>{{ $admin->login_at ? \Carbon\Carbon::parse($admin->login_at)->format('d M Y H:i') : '-' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted pe-2">Hak Akses:</th>
                                        <td>
                                            @switch($admin->role)
                                                @case(1)
                                                    Super-Admin
                                                @break

                                                @case(2)
                                                    Admin
                                                @break

                                                @case(3)
                                                    Operasional
                                                @break

                                                @case(4)
                                                    Konten-Pembelajaran
                                                @break

                                                @case(5)
                                                    Layanan-Pengguna
                                                @break

                                                @default
                                                    Tidak Aktif
                                            @endswitch
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            {{-- input file foto --}}
                            <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                type="file" id="photoInput" name="photo" accept="image/*" hidden>
                        </div>

                        {{-- ================= FORM LENGKAP ================= --}}
                        <div class="col-md-8">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">Nama Lengkap</label>
                                    <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                        type="text" name="name" class="form-control"
                                        value="{{ old('name', $admin->name) }}" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Username</label>
                                    <input aautocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                        autocomplete="off" type="text" name="username" class="form-control"
                                        value="{{ old('username', $admin->username) }}" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Tanggal Masuk</label>
                                    <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                        type="date" name="date_in" class="form-control"
                                        value="{{ old('date_in', $admin->date_in) }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Jabatan / Posisi</label>
                                    <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                        type="text" name="position" class="form-control"
                                        value="{{ old('position', $admin->position) }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">No. Telepon</label>
                                    <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                        type="number" name="phone" class="form-control"
                                        value="{{ old('phone', $admin->phone) }}">
                                </div>


                            </div>

                            {{-- Ganti Password --}}
                            <hr class="my-4">
                            <h6 class="fw-bold mb-3">Ganti Password</h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Password Lama</label>
                                    <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                        type="password" name="current_password" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Password Baru</label>
                                    <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                        type="password" name="new_password" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Konfirmasi Password</label>
                                    <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                        type="password" name="new_password_confirmation" class="form-control">
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-add w-100">
                                    <i class="fas fa-save me-2"></i> Simpan Perubahan
                                </button>
                            </div>

                </form>

                {{-- Nonaktifkan akun --}}
                <form action="{{ route('admin.profil.destroy') }}" method="POST" class="mt-3">
                    @csrf
                    <button type="submit" class="btn btn-sm-2 w-100">
                        <i class="fas fa-user-slash me-2"></i> Nonaktifkan Akun
                    </button>
                </form>
            </div>

        </div>
    </div>
    </div>

    </div>
@endsection

@section('js')
    <script>
        // Ganti foto preview
        const btnChangePhoto = document.getElementById('btnChangePhoto');
        const photoInput = document.getElementById('photoInput');
        const imgPreview = document.getElementById('imgPreview');

        btnChangePhoto.addEventListener('click', () => photoInput.click());

        photoInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (event) => imgPreview.src = event.target.result;
                reader.readAsDataURL(file);
            }
        });
    </script>
@endsection
