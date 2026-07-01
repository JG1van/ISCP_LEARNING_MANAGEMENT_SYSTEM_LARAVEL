<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'SciMedia Online')</title>

    <style>
        :root {
            --bg-image: url('{{ asset('images/bg-2.svg') }}');
        }
    </style>

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}?v={{ time() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    @php
        // Detect controller & method yang sedang aktif
        $currentAction = request()->route()->getActionName();
        $controller = explode('@', class_basename($currentAction))[0];

        // Default: controller tanpa ALLOWED_ROLES = akses bebas
        $allowedRoles = null;

        // Cek apakah controller ada dalam namespace Admin
        if (class_exists("App\\Http\\Controllers\\Admin\\$controller")) {
            $fullClass = "App\\Http\\Controllers\\Admin\\$controller";

            // Jika controller punya konstanta ALLOWED_ROLES → gunakan sebagai pembatas
            if (defined("$fullClass::ALLOWED_ROLES")) {
                $allowedRoles = $fullClass::ALLOWED_ROLES;
            }
        }

        $userRole = Auth::user()->role ?? 0;

        // Jika allowedRoles == null → tidak ada pembatas → otomatis boleh
        $allowedForThisUser = $allowedRoles === null ? true : in_array($userRole, $allowedRoles);
    @endphp


    {{-- ================= SIDEBAR ================= --}}
    @include('admin.layouts.partials.sidebar')

    {{-- ================= MAIN CONTENT ================= --}}
    <main class="main-content p-3">

        <header class="fixed-top w-100 d-flex justify-content-center pt-4 pb-2   ribbon-container">

            <div class="ribbon-wrapper ">

                <!-- Left Tail -->
                <div class="ribbon-tail-left">
                    <div class="ribbon-tail-inner"></div>
                </div>

                <!-- Right Tail -->
                <div class="ribbon-tail-right">
                    <div class="ribbon-tail-inner"></div>
                </div>

                <!-- Main Center Ribbon -->
                <div class="ribbon-main d-flex align-items-center justify-content-center position-relative px-5">

                    <!-- Tombol Burger (mobile) -->
                    <div class="d-md-none position-absolute start-0 ms-4">
                        <button class="btn btn-outline-light py-1 px-2" data-bs-toggle="offcanvas"
                            data-bs-target="#sidebarMobile">
                            <i class="fas fa-bars fs-1"></i>
                        </button>
                    </div>

                    <!-- Judul dari layout kedua -->
                    <h3 class="m-0 text-center fw-bold">
                        @yield('page_title', 'Admin')
                    </h3>

                    <!-- Info User di kanan -->
                    <a href="{{ route('admin.profil.index') }}"
                        class="position-absolute end-0 d-flex align-items-center gap-2 me-4 text-white text-decoration-none">
                        <span class="d-none d-md-inline"
                            style="font-size: 50%">{{ Auth::user()->username ?? 'Admin' }}</span>
                        <i class="fas fa-user-circle fs-1"></i>
                    </a>

                </div>
            </div>

        </header>



        {{-- ================= NOTIFIKASI SWEETALERT ================= --}}
        @if (session('success'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: "{{ session('success') }}",
                    showConfirmButton: false,
                    timer: 2000
                })
            </script>
        @endif

        @if (session('error'))
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#B05B3B',
                })
            </script>
        @endif
        @if ($errors->any())
            <script>
                let errorMessages = `{!! implode('<br>', $errors->all()) !!}`;

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    html: errorMessages,
                });
            </script>
        @endif
        @if (session('notif-success'))
            <div class="alert custom-alert-1 alert-dismissible fade show mx-3" role="alert">
                {{ session('notif-success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('notif-error'))
            <div class="alert custom-alert-1 alert-dismissible fade show mx-3" role="alert">
                {{ session('notif-error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('success_html'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    html: `{!! session('success_html') !!}`,
                    showConfirmButton: false,
                    timer: 5000
                })
            </script>
        @endif
        {{-- ================= KONTEN DINAMIS ================= --}}
        <!-- Tombol Kembali Global -->
        @php
            $currentUrl = request()->path(); // contoh: "admin/pelajaran/create"
        @endphp
        <div class="alas">

            {{-- CEK ROLE TIDAK DIIZINKAN --}}
            <div>
                {{-- CEK ROLE TIDAK DIIZINKAN --}}
                @if (isset($allowedForThisUser) && !$allowedForThisUser)
                    <div class="d-flex justify-content-center my-5">
                        <div class="alert alert-danger text-center p-4 w-75 shadow-sm rounded-4">

                            <h3 class="fw-bold mb-3">
                                <i class="fas fa-ban me-2"></i> Akses Ditolak
                            </h3>

                            <p class="mb-3">
                                Role Anda saat ini <strong>tidak sesuai</strong> untuk mengakses halaman ini.<br>
                                Halaman yang Anda buka memiliki batasan akses khusus sesuai kategori peran.
                            </p>

                            <p class="text-muted mb-4">
                                Jika Anda merasa ini adalah sebuah kesalahan, <br>
                                <strong>harap segera menghubungi Super-Admin</strong> untuk peninjauan hak akses.
                            </p>

                            {{-- Role User Sekarang --}}
                            <div class="mb-3">
                                <strong class="d-block mb-1">Role Anda:</strong>
                                @switch($userRole)
                                    @case(1)
                                        <span class="badge bg-dark px-3 py-2">Super-Admin</span>
                                    @break

                                    @case(2)
                                        <span class="badge bg-primary px-3 py-2">Admin</span>
                                    @break

                                    @case(3)
                                        <span class="badge bg-success px-3 py-2">Operasional</span>
                                    @break

                                    @case(4)
                                        <span class="badge bg-warning text-dark px-3 py-2">Konten-Pembelajaran</span>
                                    @break

                                    @case(5)
                                        <span class="badge bg-info text-dark px-3 py-2">Layanan-Pengguna</span>
                                    @break

                                    @default
                                        <span class="badge bg-secondary px-3 py-2">Tidak Aktif</span>
                                @endswitch
                            </div>

                            {{-- Daftar role yang diperbolehkan oleh controller --}}
                            <div class="mt-4">
                                <strong class="d-block mb-2">Role yang diizinkan untuk halaman ini:</strong>

                                @foreach ($allowedRoles as $r)
                                    @switch($r)
                                        @case(1)
                                            <span class="badge bg-dark px-3 py-2 m-1">Super-Admin</span>
                                        @break

                                        @case(2)
                                            <span class="badge bg-primary px-3 py-2 m-1">Admin</span>
                                        @break

                                        @case(3)
                                            <span class="badge bg-success px-3 py-2 m-1">Operasional</span>
                                        @break

                                        @case(4)
                                            <span class="badge bg-warning text-dark px-3 py-2 m-1">Konten-Pembelajaran</span>
                                        @break

                                        @case(5)
                                            <span class="badge bg-info text-dark px-3 py-2 m-1">Layanan-Pengguna</span>
                                        @break

                                        @default
                                            <span class="badge bg-secondary px-3 py-2 m-1">Tidak Aktif</span>
                                    @endswitch
                                @endforeach
                            </div>

                        </div>
                    </div>

                    @php
                        return;
                    @endphp
                @endif

            </div>

            @php
                // Hitung jumlah segmen URL (contoh: admin/pelajaran/1/materi = 4)
                $segmentCount = count(Request::segments());
            @endphp

            @if ($segmentCount > 3 && Request::segment(1) === 'admin')
                <div class="mb-3">
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Kembali
                    </a>
                </div>
            @endif

            {{-- ================= KONTEN DINAMIS ================= --}}
            @yield('content')

        </div>



    </main>

    {{-- ================= SCRIPT ================= --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    {{-- ================= SWEETALERT KONFIRMASI GLOBAL ================= --}}
    <script>
        function showConfirm(message, onConfirm) {
            Swal.fire({
                title: 'Konfirmasi',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#B05B3B',
                cancelButtonColor: '#D79771',
                confirmButtonText: 'Ya, lanjutkan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed && typeof onConfirm === 'function') {
                    onConfirm();
                }
            });
        }


        function showToast(type, message) {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });
            Toast.fire({
                icon: type,
                title: message
            });
        }
    </script>

    {{-- Script halaman spesifik --}}
    @yield('js')

</body>

</html>
