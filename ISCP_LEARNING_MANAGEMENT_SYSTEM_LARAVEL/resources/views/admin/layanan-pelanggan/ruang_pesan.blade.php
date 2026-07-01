@extends('admin.layouts.app')

@section('title', 'Ruang Percakapan ')
@section('page_title', 'Ruang Percakapan')

@section('content')

    @if (!$room)
        <div class="alert alert-warning text-center mt-4">
            <h5 class="fw-bold text-danger">⚠️ Ruangan Tidak Ditemukan</h5>
            <p>Kode ruangan ini sudah tidak tersedia atau telah dihapus.</p>
            <a href="{{ route('admin.layanan-pelanggan.index') }}" class="btn btn-add mt-2">Kembali</a>
        </div>
    @else
        <div class="row mt-4 mx-2" id="chatUI">

            <!-- 1. CHAT AREA -->
            <div class="col-lg-7 col-md-12 mb-3 d-flex">
                <div class="card flex-fill h-100" style="overflow: hidden;">
                    <div class="card-body d-flex flex-column h-100" style="overflow: hidden;">

                        <!-- HEADER CHAT ADMIN -->
                        <div class="d-flex justify-content-between align-items-center mb-3 w-100">
                            <h5 class="mb-0 flex-grow-1 text-center">
                                Kode Ruangan: <b>{{ $room->room_code }}</b>
                            </h5>
                        </div>

                        <!-- CHAT BOX -->
                        <div id="chatBox" class="flex-grow-1 overflow-auto mb-3"></div>

                        <!-- FORM KIRIM PESAN -->
                        <form id="sendForm">
                            @csrf
                            <input type="hidden" id="roomId" value="{{ $room->id }}">
                            <input type="hidden" id="sender"
                                value="Admin({{ auth()->user()->username . '#' . auth()->user()->id }})">
                            <input type="hidden" id="currentUser"
                                value="Admin({{ auth()->user()->username . '#' . auth()->user()->id }})">

                            <div class="input-group">
                                <input type="text" id="msgInput" class="form-control" placeholder="Ketik pesan..."
                                    autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                    inputmode="none">
                                <button class="btn btn-add" style="height: 36.5px">Kirim</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

            <!-- 2. FILE LAMPIRAN  -->
            <div class="col-lg-5 col-md-12 mb-3 d-flex">
                <div class="card flex-fill h-100 d-flex flex-column">

                    <!-- HEADER -->
                    <div class="card-header text-center " style="background: #fff; overflow: hidden; min-height: 50px;">
                        <b>Lampiran Gambar</b>
                    </div>

                    <!-- LIST FILE (SCROLL) -->
                    <div class="p-3 overflow-auto flex-grow-1">
                        <div id="logGambar" class="row g-2"></div>
                    </div>

                    <!-- UPLOAD GAMBAR -->
                    <div class="p-3 border-top">
                        <form id="uploadFileForm" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="input-group">
                                <input type="file" id="uploadFileInput" name="file" class="form-control"
                                    accept="image/*">
                                <button type="button" id="uploadFileBtn" class="btn btn-add">Kirim</button>
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
    @endif

    <!-- Modal Preview Gambar -->
    <div class="modal fade" id="imagePreviewModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0">
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

@endsection

@section('js')

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
    @if ($room)
        <script>
            document.addEventListener("DOMContentLoaded", function() {

                const uploadBtn = document.getElementById("uploadFileBtn");
                const fileInput = document.getElementById("uploadFileInput");

                // =========== UPLOAD FILE (ADMIN) ===========
                uploadBtn.addEventListener("click", function() {

                    let file = fileInput.files[0];

                    if (!file) {
                        showInfo("Pilih file terlebih dahulu!");
                        return;
                    }

                    // === UBAH TOMBOL JADI LOADING ===
                    uploadBtn.disabled = true;
                    uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mengirim..';

                    let formData = new FormData();
                    formData.append("file", file);
                    formData.append("_token", "{{ csrf_token() }}");

                    let xhr = new XMLHttpRequest();
                    xhr.open("POST", "{{ route('admin.layanan-pelanggan.upload', $room->id) }}", true);

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

                        // === RESET BTN ===
                        uploadBtn.disabled = false;
                        uploadBtn.innerHTML = 'Kirim';
                    };

                    xhr.send(formData);
                });
            });
            // =========== PREVIEW GAMBAR ===========
            function previewImage(src) {
                document.getElementById("modalPreviewImg").src = src;

                let modal = new bootstrap.Modal(document.getElementById("imagePreviewModal"));
                modal.show();
            }

            // =========== ZOOM GAMBAR ===========
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
            const endpoint = "/admin/layanan-pelanggan-admin/files";

            // realtime load file
            enableRealtime(roomId, endpoint, "logGambar");

            // jika ada tombol upload, otomatis tidak dipakai lagi (hapus JS-nya)
        </script>
    @endif
@endsection
