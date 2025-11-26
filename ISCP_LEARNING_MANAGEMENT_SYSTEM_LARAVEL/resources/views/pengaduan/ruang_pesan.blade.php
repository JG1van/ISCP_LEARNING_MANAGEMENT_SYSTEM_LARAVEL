<!DOCTYPE html>
<html lang="id" class="bg-light">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Ruang Pengaduan</title>



    <link rel="stylesheet" href="{{ asset('css/styles.css') }}?v={{ time() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @php
        $isAdminMode = $room->chat_status === 'Admin';
    @endphp

</head>

<body class="bg-light">
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
    <main>
        <div class="container mt-4" id="main-content" style="{{ $isAdminMode ? 'display:none;' : '' }}">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="mb-0">Ruang Pengaduan</h3>
                <div class="d-flex align-items-center">
                    <div class="me-3"><strong>Kode Pengaduan:</strong> <span
                            id="complaintCode">{{ $room->complaint_code }}</span></div>

                    <form action="{{ route('pengaduan.finish', $room->complaint_code) }}" method="POST"
                        class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm-1">Selesai</button>
                    </form>
                </div>
            </div>

            <p class="text-muted mb-3">Status: <span class="fw-bold">{{ $room->chat_status }}</span></p>

            <ul class="nav nav-tabs mb-3" id="kategoriTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active text-dark" id="tab-umum-btn" data-bs-toggle="tab"
                        data-bs-target="#tab-umum" type="button" role="tab">Umum</button>
                </li>
                <li class="nav-item " role="presentation">
                    <button class="nav-link text-dark" id="tab-siswa-btn" data-bs-toggle="tab"
                        data-bs-target="#tab-siswa" type="button" role="tab">Siswa</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link text-dark" id="tab-guru-btn" data-bs-toggle="tab" data-bs-target="#tab-guru"
                        type="button" role="tab">Guru</button>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="tab-umum" role="tabpanel">
                    @include('pengaduan.partials.kategori-list', ['items' => $tabUmum])
                </div>

                <div class="tab-pane fade" id="tab-siswa" role="tabpanel">
                    @include('pengaduan.partials.kategori-list', ['items' => $tabSiswa])
                </div>

                <div class="tab-pane fade" id="tab-guru" role="tabpanel">
                    @include('pengaduan.partials.kategori-list', ['items' => $tabGuru])
                </div>
            </div>
        </div>

        <div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Login Diperlukan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="loginError" class="alert alert-danger d-none" role="alert"></div>

                        <form id="loginForm" onsubmit="loginAllSubmit(); return false;">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input name="username" required class="form-control" autocomplete="username">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input name="password" type="password" required class="form-control"
                                    autocomplete="current-password">
                            </div>

                            <div class="d-grid">
                                <button id="loginSubmitBtn" type="submit" class="btn btn-primary">
                                    <span id="loginSubmitLabel">Login</span>
                                </button>
                            </div>
                        </form>

                        <div class="mt-2 text-muted small">Login sebagai Guru / Siswa sesuai akun.</div>
                    </div>
                </div>
            </div>
        </div>
        <div id="adminButtonContainer" style="{{ $isAdminMode ? 'display:none;' : '' }}">
            <div class="container">
                <button id="hubungiAdminBtn" class="btn btn-add w-100" onclick="showChat()">Hubungi
                    Admin</button>
            </div>
        </div>

        <div class="container" id="chatUI" style="{{ $isAdminMode ? 'display:block;' : 'display:none;' }}">
            <div class="card mt-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Kode Pengaduan: <b>{{ $room->complaint_code }}</b></h5>
                        <form action="{{ route('pengaduan.finish', $room->complaint_code) }}" method="POST"
                            class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm-1">Selesai</button>
                        </form>
                    </div>

                    <div id="chatBox"></div>

                    <form id="sendForm" class="mt-3">
                        @csrf
                        <input type="hidden" id="roomId" value="{{ $room->id }}">
                        <input type="hidden" id="sender" value="Pelapor">
                        <div class="input-group">
                            <input type="text" id="msgInput" class="form-control" placeholder="Ketik pesan...">
                            <button class="btn btn-primary" type="submit">Kirim</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

        <div class="modal fade" id="problemModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content" style="border-radius:14px; overflow:hidden;">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="problemTitle">
                        </h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div id="solutionWrapper" class="mb-4">
                            <div class="fw-bold text-secondary mb-2">Penjelasan Solusi</div>
                            <div id="problemSolution" style="font-size:15px; line-height:1.5;"></div>
                        </div>

                        <div id="videoWrapper" class="mb-4" style="display:none;">
                            <div class="fw-bold text-secondary mb-2">Video Panduan</div>
                            <div id="problemVideoContainer"></div>
                        </div>

                        <div id="fileWrapper" class="mb-2" style="display:none;">
                            <div class="fw-bold text-secondary mb-2">File Panduan</div>
                            <div id="problemFileContainer"></div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button id="hubungiAdminBtn" class="btn btn-add w-100" onclick="showChat()">Hubungi
                            Admin</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="videoModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5>Video Panduan</h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div id="videoHolder"></div>
                    </div>
                </div>
            </div>
        </div>


        <script>
            window.FirebaseConfig = {
                apiKey: "{{ config('firebase.api_key') }}",
                authDomain: "{{ config('firebase.auth_domain') }}",
                databaseURL: "{{ config('firebase.database_url') }}",
                projectId: "{{ config('firebase.project_id') }}",
                messagingSenderId: "{{ config('firebase.messaging_sender_id') }}",
                appId: "{{ config('firebase.app_id') }}"
            };
        </script>

        <script type="module" src="/js/complaint-realtime.js"></script>

    </main>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
</body>

<script>
    // KONFIRMASI & TOAST
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


    // LOAD KATEGORI KE OBJECT JS
    const problems = {};

    @foreach ($tabUmum as $c)
        problems["{{ $c->id }}"] = {
            id: {{ $c->id }},
            title: {!! json_encode($c->name) !!},
            solution: {!! json_encode($c->solution_text) !!},
            file: {!! json_encode($c->guide_file) !!},
            video: {!! json_encode($c->guide_video) !!},
            level: {!! json_encode($c->level) !!}
        };
    @endforeach

    @foreach ($tabSiswa as $c)
        problems["{{ $c->id }}"] = {
            id: {{ $c->id }},
            title: {!! json_encode($c->name) !!},
            solution: {!! json_encode($c->solution_text) !!},
            file: {!! json_encode($c->guide_file) !!},
            video: {!! json_encode($c->guide_video) !!},
            level: {!! json_encode($c->level) !!}
        };
    @endforeach

    @foreach ($tabGuru as $c)
        problems["{{ $c->id }}"] = {
            id: {{ $c->id }},
            title: {!! json_encode($c->name) !!},
            solution: {!! json_encode($c->solution_text) !!},
            file: {!! json_encode($c->guide_file) !!},
            video: {!! json_encode($c->guide_video) !!},
            level: {!! json_encode($c->level) !!}
        };
    @endforeach



    // BUKA DETAIL MASALAH
    function openProblemDetail(id) {
        const isLoggedIn = "{{ auth()->check() ? 'yes' : 'no' }}";
        const cat = problems[id];

        if (!cat) {
            showToast("error", "Kategori tidak ditemukan!");
            return;
        }


        // Jika kategori bukan umum → wajib login
        if (cat.level !== "Umum" && isLoggedIn === "no") {

            localStorage.setItem('open_after_login', "{{ $room->complaint_code }}");
            localStorage.setItem('open_after_login_category', id);

            const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
            loginModal.show();
            return;
        }

        // Assign kategori
        fetch("{{ route('pengaduan.assign_category', $room->complaint_code) }}", {
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                category_id: id
            })
        }).catch(() => {});


        // =============================
        // ISI MODAL
        // =============================
        document.getElementById('problemTitle').textContent =
            `${cat.title} (${cat.level})`;

        document.getElementById('problemSolution').innerHTML =
            `<p>${cat.solution ?? 'Silakan baca panduan dan tonton video.'}</p>`;

        // VIDEO
        const videoWrapper = document.getElementById('videoWrapper');
        const videoBox = document.getElementById('problemVideoContainer');

        if (cat.video) {
            const isIframe = String(cat.video).includes('<iframe');
            videoBox.innerHTML = `
            <div class="video-wrapper">
                ${ isIframe ? cat.video : `<iframe src="${cat.video}" frameborder="0" allowfullscreen></iframe>` }
            </div>
        `;
            videoWrapper.style.display = 'block';
        } else {
            videoWrapper.style.display = 'none';
            videoBox.innerHTML = '';
        }

        // FILE
        const fileWrapper = document.getElementById('fileWrapper');
        const fileBox = document.getElementById('problemFileContainer');

        if (cat.file) {
            fileBox.innerHTML = `
            <a href="/storage/guide_files/${cat.file}" class="btn btn-sm-2 w-100" target="_blank">
                Buka File Panduan
            </a>`;
            fileWrapper.style.display = 'block';
        } else {
            fileWrapper.style.display = 'none';
            fileBox.innerHTML = '';
        }

        new bootstrap.Modal(document.getElementById('problemModal')).show();
    }



    // LOGIN AJAX (Guru / Siswa)
    async function loginAllSubmit() {
        const form = document.getElementById('loginForm');
        const submitBtn = document.getElementById('loginSubmitBtn');
        const submitLabel = document.getElementById('loginSubmitLabel');
        const errorBox = document.getElementById('loginError');

        errorBox.classList.add('d-none');
        errorBox.textContent = '';

        const data = new FormData(form);

        const roomCode = localStorage.getItem('open_after_login');
        const categoryId = localStorage.getItem('open_after_login_category');

        if (roomCode) data.append("room_code", roomCode);
        if (categoryId) data.append("category_id", categoryId);

        submitBtn.disabled = true;
        submitLabel.textContent = 'Tunggu...';

        try {
            const res = await fetch("{{ route('login.ajax') }}", {
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    'Accept': "application/json"
                },
                body: data
            });

            const json = await res.json();

            if (json.status === 'success') {

                const modalEl = document.getElementById('loginModal');
                const modal = bootstrap.Modal.getInstance(modalEl);
                modal.hide();

                setTimeout(() => {
                    window.location.reload(true);
                }, 300);

                return;
            }

            errorBox.textContent = json.message || "Gagal login.";
            errorBox.classList.remove('d-none');

        } catch (e) {
            errorBox.textContent = "Terjadi kesalahan koneksi.";
            errorBox.classList.remove('d-none');
        } finally {
            submitBtn.disabled = false;
            submitLabel.textContent = 'Login';
        }
    }



    // AUTO OPEN SETELAH LOGIN
    window.addEventListener('load', function() {

        const roomCode = localStorage.getItem('open_after_login');
        const categoryId = localStorage.getItem('open_after_login_category');
        const isLoggedIn = "{{ auth()->check() ? 'yes' : 'no' }}";

        if (roomCode && categoryId && isLoggedIn === "yes") {

            localStorage.removeItem('open_after_login');
            localStorage.removeItem('open_after_login_category');

            setTimeout(() => openProblemDetail(categoryId), 200);
        }
    });



    // BUKA CHAT
    function showChat() {

        if ("{{ $room->chat_status }}" === "Sistem") {
            fetch("{{ route('pengaduan.set_admin', $room->id) }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                }
            }).catch(() => {});
        }

        document.getElementById('main-content').style.display = 'none';
        document.getElementById('adminButtonContainer').style.display = 'none';
        document.getElementById('chatUI').style.display = 'block';
    }

    window.openProblemDetail = openProblemDetail;


    // POPUP KODE PENGADUAN
    document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
            title: 'Kode Pengaduan',
            html: `
            <div class="mt-2">
                <b id="kodePengaduan" style="font-size: 18px; letter-spacing: 1px;">
                    {{ $room->complaint_code }}
                </b>
                <br><br>
                <span class="text-muted" style="font-size: 14px;">
                    Simpan kode ini untuk membuka kembali ruang pengaduan ini.
                </span>
            </div>
        `,
            icon: 'info',
            confirmButtonText: 'Mengerti',
            allowOutsideClick: false,
            allowEscapeKey: false,
        }).then(() => {
            const kode = document.getElementById('kodePengaduan').innerText;
            navigator.clipboard.writeText(kode).then(() => {
                showToast("success", "Kode berhasil disalin!");
            });
        });
    });
</script>

</html>
