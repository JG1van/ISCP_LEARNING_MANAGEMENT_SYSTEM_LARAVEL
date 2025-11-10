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

    {{-- ================= SIDEBAR ================= --}}
    @include('layouts.partials.sidebar')

    {{-- ================= MAIN CONTENT ================= --}}
    <main class="main-content p-3">

        <div
            class="header d-flex justify-content-center align-items-center mb-4 
           bg-custom shadow-sm  px-4 py-2 border">

            <!-- Tombol Burger (mobile) -->
            <div class="d-md-none position-absolute start-0 ms-2">
                <button class="btn btn-outline-dark" data-bs-toggle="offcanvas" data-bs-target="#sidebarMobile">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <!-- Judul di Tengah -->
            <h3 class="m-0 text-center">@yield('page_title', 'Admin')</h3>

            <!-- Info User di Kanan -->
            <a href="{{ route('admin.profil.index') }}"
                class="tu-info text-black text-decoration-none position-absolute end-0 d-flex align-items-center gap-2 me-2">
                <span class="d-none d-md-inline">{{ Auth::user()->username ?? 'Admin' }}</span>
                <i class="fas fa-user-circle fs-5"></i>
            </a>
        </div>


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
            @php
                // Hitung jumlah segmen URL (contoh: admin/pelajaran/1/materi = 4)
                $segmentCount = count(Request::segments());
            @endphp

            @if ($segmentCount > 2 && Request::segment(1) === 'admin')
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
        /**
         * SweetAlert versi global untuk konfirmasi aksi
         * @param {string} message - pesan konfirmasi
         * @param {function} onConfirm - callback saat klik 'Ya'
         */
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

        /**
         * SweetAlert versi toast (notifikasi cepat)
         * @param {string} type - success, error, info, warning
         * @param {string} message - pesan singkat
         */
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
