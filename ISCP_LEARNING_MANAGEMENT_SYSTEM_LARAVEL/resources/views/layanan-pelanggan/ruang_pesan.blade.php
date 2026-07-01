<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Ruang Layanan Pelanggan </title>



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
<style>
    :root {
        --bg-image: url('{{ asset('images/bg-10.webp') }}');
    }
</style>

<body style="min-height:600px ">
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
        <div class="alas container mt-4" id="main-content"
            style="background:#fff !important; {{ $isAdminMode ? 'display:none;' : '' }}">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="mb-0">Ruang Layanan Pelanggan </h3>
                <div class="d-flex align-items-center">
                    <form id="formSelesaiTop" action="{{ route('layanan-pelanggan.finish', $room->room_code) }}"
                        method="POST" class="d-inline">
                        @csrf
                        <button type="button" id="btnSelesaiTop" class="btn btn-alt-1">Selesai</button>
                    </form>

                </div>
            </div>
            <div class="me-3"><strong>Kode Ruangan:</strong> <span id="RoomCode">{{ $room->room_code }}</span></div>
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
                    @include('layanan-pelanggan.partials.kategori-list', ['items' => $tabUmum])
                </div>

                <div class="tab-pane fade" id="tab-siswa" role="tabpanel">
                    @include('layanan-pelanggan.partials.kategori-list', ['items' => $tabSiswa])
                </div>

                <div class="tab-pane fade" id="tab-guru" role="tabpanel">
                    @include('layanan-pelanggan.partials.kategori-list', ['items' => $tabGuru])
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
                <button id="hubungiAdminBtn" class="btn btn-add w-100" onclick="showChat()">Mulai Percakapan</button>
            </div>
        </div>

        <div class="row mt-5 mx-3" id="chatUI"
            @if (!$isAdminMode) style="visibility:hidden; height:0; overflow:hidden;" @endif>

            <div class="col-lg-8 col-md-12 mb-3 d-flex">
                <div class="card flex-fill h-100" style="overflow: hidden;">
                    <div class="card-body d-flex flex-column h-100" style="overflow: hidden;">

                        <!-- HEADER -->
                        <div class="d-flex justify-content-between align-items-center mb-3 w-100">
                            <button id="panggilLagiBtn" class="btn btn-alt-2">Panggilan Ulang</button>

                            <h5 class="mb-0 flex-grow-1 text-center">
                                Kode Ruangan: <b>{{ $room->room_code }}</b>
                            </h5>

                            <form id="formSelesaiBottom"
                                action="{{ route('layanan-pelanggan.finish', $room->room_code) }}" method="POST"
                                class="d-inline">
                                @csrf
                                <button type="button" id="btnSelesaiBottom" class="btn btn-alt-1">Selesai</button>
                            </form>
                        </div>

                        <!-- CHAT BOX -->
                        <div id="chatBox" class="flex-grow-1 overflow-auto mb-3">
                        </div>

                        <!-- FORM KIRIM PESAN -->
                        <form id="sendForm">
                            @csrf
                            <input type="hidden" id="roomId" value="{{ $room->id }}">
                            <input type="hidden" id="sender" value="Pelapor">
                            <input type="hidden" id="currentUser" value="Pelapor">

                            <div class="input-group">
                                <input type="text" id="msgInput" class="form-control"
                                    placeholder="Ketik pesan..." autocomplete="off" autocorrect="off"
                                    autocapitalize="off" spellcheck="false" inputmode="none">
                                <button class="btn btn-add" style="height:36.5px" type="submit">Kirim</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

            <!-- ===================== FILE LAMPIRAN (4 kolom) ===================== -->
            <div class="col-lg-4 col-md-12 mb-3 d-flex">
                <div class="card flex-fill h-100 d-flex flex-column">

                    <!-- HEADER -->
                    <div class="card-header text-center "
                        style="background: #fff; overflow: hidden; min-height: 50px;">
                        <b>Lampiran Gambar</b>
                    </div>

                    <!-- LIST FILE (scroll) -->
                    <div class="p-3 overflow-auto flex-grow-1">
                        <div id="logGambar" class="row g-2"></div>
                    </div>

                    <!-- UPLOAD FORM -->
                    <div class="p-3 border-top">
                        <form id="uploadFileForm" enctype="multipart/form-data">
                            @csrf

                            <div class="input-group">
                                <!-- HANYA MENERIMA GAMBAR -->
                                <input type="file" id="uploadFileInput" name="file" class="form-control"
                                    accept="image/*">

                                <button type="button" id="uploadFileBtn" class="btn btn-add">
                                    Kirim
                                </button>
                            </div>

                            {{-- <div id="uploadProgressWrap" class="mt-2" style="display:none;">
                                <div class="progress" style="height:6px;">
                                    <div id="uploadProgressBar" class="progress-bar" style="width:0%"></div>
                                </div>
                            </div> --}}
                        </form>
                    </div>

                </div>
            </div>


        </div>


        <!-- MODAL PREVIEW GAMBAR -->
        <div class="modal fade" id="imagePreviewModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header border-0 ">
                        <h5 class="modal-title">Preview Gambar</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-0 text-center">

                        <!-- Gambar akan diubah lewat JavaScript -->
                        <img id="modalPreviewImg" src="" class="img-fluid"
                            style="transition: transform 0.3s; cursor: zoom-in;">

                    </div>

                    <div class="modal-footer justify-content-between">
                        <span>Klik gambar untuk zoom</span>
                        <button class="btn btn-alt-1" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL DETAIL MASALAH -->
        <div class="modal fade" id="problemModal" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="problemTitle"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <div id="problemSolution" class="mb-4"></div>

                        <div id="videoWrapper" class="mb-4" style="display:none;">
                            <h6>Video Panduan</h6>
                            <div id="problemVideoContainer"></div>
                        </div>

                        <div id="fileWrapper" class="mb-3" style="display:none;">
                            <h6>File Panduan</h6>
                            <div id="problemFileContainer"></div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button id="hubungiAdminBtn" class="btn btn-add w-100" onclick="showChat()">Mulai
                            Percakapan</button>
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

        <script type="module" src="/js/cs-realtime.js"></script>

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
            cancelButtonText: 'Batal',
            confirmButtonText: 'Ya, lanjutkan'

        }).then((result) => {
            if (result.isConfirmed && typeof onConfirm === 'function') {
                onConfirm();
            }
        });
    }

    // --- SweetAlert Helper ---
    function showInfo(message) {
        Swal.fire({
            title: 'Informasi',
            text: message,
            icon: 'info',
            confirmButtonColor: '#B05B3B',
            confirmButtonText: 'OK'
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

            localStorage.setItem('open_after_login', "{{ $room->room_code }}");
            localStorage.setItem('open_after_login_category', id);

            const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
            loginModal.show();
            return;
        }

        // Assign kategori
        fetch("{{ route('layanan-pelanggan.assign_category', $room->room_code) }}", {
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

        const fileWrapper = document.getElementById('fileWrapper');
        const fileBox = document.getElementById('problemFileContainer');

        fileWrapper.style.display = 'block'; // selalu tampilkan wrapper

        if (cat.file) {
            fileBox.innerHTML = `
        <a href="/storage/guide_files/${cat.file}" 
           class="btn btn-alt-2 w-100">
            Buka File Panduan
        </a>`;
        } else {
            fileBox.innerHTML = `
        <button class="btn btn-secondary w-100" disabled>
            File Tidak Tersedia
        </button>`;
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

        // 1. Kirim pesan sistem SEBELUM apa pun
        if ("{{ $room->chat_status }}" === "Sistem") {
            fetch("{{ route('layanan-pelanggan.set_admin', $room->id) }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                }
            }).catch(() => {});
        }

        // 2. Baru jalankan UI
        const modalEl = document.getElementById('problemModal');
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.hide();

        document.getElementById('main-content').style.display = 'none';
        document.getElementById('adminButtonContainer').style.display = 'none';

        const chatUI = document.getElementById("chatUI");
        chatUI.style.visibility = "visible";
        chatUI.style.height = "auto";
        chatUI.style.overflow = "visible";
    }

    window.openProblemDetail = openProblemDetail;


    // POPUP KODE PENGADUAN
    document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
            title: 'Kode Ruangan Anda:',
            html: `
            <div class="mt-2">
                <b id="kodePengaduan" style="font-size: 18px; letter-spacing: 1px;">
                    {{ $room->room_code }}
                </b>
                <br><br>
                <span class="text-muted" style="font-size: 14px;">
                    Simpan kode ini untuk membuka kembali ruang ini.
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

    const COOLDOWN_KEY = "panggilLagiCooldown";
    const COOLDOWN_TIME = 5 * 60; // 5 menit (300 detik)
    const btn = document.getElementById("panggilLagiBtn");

    // --- Fungsi Mulai Timer ---
    function startCooldown(seconds) {
        btn.disabled = true;

        let remaining = seconds;

        const interval = setInterval(() => {
            if (remaining <= 0) {
                clearInterval(interval);
                btn.disabled = false;
                btn.innerHTML = "Panggilan Ulang";
                localStorage.removeItem(COOLDOWN_KEY);
                return;
            }

            const m = String(Math.floor(remaining / 60)).padStart(2, "0");
            const s = String(remaining % 60).padStart(2, "0");
            btn.innerHTML = `${m}:${s}`;
            remaining--;

            // Simpan waktu end ke localStorage
            localStorage.setItem(COOLDOWN_KEY, Date.now() + (remaining * 1000));
        }, 1000);
    }

    // --- Cek apakah sedang cooldown saat halaman dibuka ---
    function checkCooldown() {
        const endTime = localStorage.getItem(COOLDOWN_KEY);
        if (!endTime) return;

        const now = Date.now();
        const diff = Math.floor((endTime - now) / 1000);

        if (diff > 0) {
            startCooldown(diff);
        } else {
            localStorage.removeItem(COOLDOWN_KEY);
        }
    }

    checkCooldown();

    // --- Event Klik Panggilan Ulang ---
    btn.addEventListener("click", function() {
        showConfirm("Yakin ingin memanggil ulang? Akan ada jeda pemakaian selama 5 menit setelahnya",
            function() {

                const roomId = document.getElementById("roomId").value;

                fetch("/layanan-pelanggan-pelapor/panggil-lagi/" + roomId, {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        showInfo("Admin telah dipanggil ulang.");

                        // Mulai cooldown 5 menit penuh
                        const endTime = Date.now() + (COOLDOWN_TIME * 1000);
                        localStorage.setItem(COOLDOWN_KEY, endTime);

                        startCooldown(COOLDOWN_TIME);
                    })
                    .catch(err => console.error(err));
            });
    });
    document.addEventListener("DOMContentLoaded", function() {

        function attachSelesaiHandler(btnId, formId) {
            const btn = document.getElementById(btnId);
            const form = document.getElementById(formId);

            if (btn && form) {
                btn.addEventListener("click", function() {
                    showConfirm("Apakah Anda yakin ingin menyelesaikan Percakapaan ini?", function() {
                        form.submit();
                    });
                });
            }
        }

        // Pasang event untuk dua tombol
        attachSelesaiHandler("btnSelesaiTop", "formSelesaiTop");
        attachSelesaiHandler("btnSelesaiBottom", "formSelesaiBottom");

    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {

        const uploadBtn = document.getElementById("uploadFileBtn");
        const fileInput = document.getElementById("uploadFileInput");

        uploadBtn.addEventListener("click", function() {

            let file = fileInput.files[0];

            if (!file) {
                showInfo("Pilih file terlebih dahulu!");
                return;
            }

            // === UBAH TOMBOL MENJADI LOADING ===
            uploadBtn.disabled = true;
            uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mengirim..';

            let formData = new FormData();
            formData.append("file", file);
            formData.append("_token", "{{ csrf_token() }}");

            let xhr = new XMLHttpRequest();
            xhr.open("POST", "{{ route('layanan-pelanggan.upload', $room->id) }}", true);

            // --- Progress Bar ---
            xhr.upload.onprogress = function(e) {
                if (e.lengthComputable) {
                    let percent = Math.round((e.loaded / e.total) * 100);
                    document.getElementById("uploadProgressWrap").style.display = "block";
                    document.getElementById("uploadProgressBar").style.width = percent + "%";
                }
            };

            xhr.onload = function() {
                let wrap = document.getElementById("uploadProgressWrap");
                let bar = document.getElementById("uploadProgressBar");

                // === KEMBALIKAN TOMBOL NORMAL ===
                uploadBtn.disabled = false;
                uploadBtn.innerHTML = 'Kirim';

                if (xhr.status === 200) {
                    let res = JSON.parse(xhr.responseText);

                    if (res.errors) {
                        showInfo(Object.values(res.errors)[0][0]);
                    } else if (!res.success) {
                        showInfo(res.message || "Upload gagal!");
                    } else {
                        loadFiles();
                        fileInput.value = "";
                    }
                } else if (xhr.status === 422) {
                    let res = JSON.parse(xhr.responseText);
                    showInfo(Object.values(res.errors)[0][0]);
                } else {
                    showInfo("Upload gagal (server error)");
                }

                wrap.style.display = "none";
                bar.style.width = "0%";
            };

            xhr.onerror = function() {
                showInfo("Tidak dapat terhubung ke server.");

                // === RESET TOMBOL ===
                uploadBtn.disabled = false;
                uploadBtn.innerHTML = 'Kirim';
            };

            xhr.send(formData);
        });

    });

    function previewImage(src) {
        document.getElementById("modalPreviewImg").src = src;

        let modal = new bootstrap.Modal(document.getElementById("imagePreviewModal"));
        modal.show();
    }
    document.addEventListener("DOMContentLoaded", () => {
        let img = document.getElementById("modalPreviewImg");
        let zoomed = false;

        img.addEventListener("click", function() {
            zoomed = !zoomed;
            this.style.transform = zoomed ? "scale(2)" : "scale(1)";
            this.style.cursor = zoomed ? "zoom-out" : "zoom-in";
        });
    });
</script>

<script type="module">
    import {
        loadFiles,
        enableRealtime
    } from "/js/cs-realtime.js";

    const roomId = "{{ $room->id }}";
    const endpoint = "/layanan-pelanggan-pelapor/files";

    // realtime load file
    enableRealtime(roomId, endpoint, "logGambar");
</script>


</html>
