@extends('admin.layouts.app')
@section('title', 'Manajemen Materi')
@section('page_title')
    Manajemen Materi - {{ $lesson->name }}
@endsection
@section('content')
    <div class="container-fluid py-3">
        <div class="col-lg-12 col-md-12 bg-white p-3 shadow-sm mb-3 rounded">
            <h5 class="fw-bold mb-3">Informasi Pelajaran</h5>
            <div class="row g-3">
                <!-- Nama Pelajaran -->
                <div class="col-md-12">
                    <label class="form-label fw-semibold">Nama Pelajaran</label>
                    <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" type="text"
                        class="form-control" value="{{ $lesson->name ?? 'Belum ditentukan' }}" readonly>
                </div>

                <!-- Kelas -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Kelas</label>
                    <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" type="text"
                        class="form-control" value="Kelas {{ $lesson->grade }}" readonly>
                </div>

                <!-- Semester -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Semester</label>
                    <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" type="text"
                        class="form-control"
                        value="{{ $lesson->semester == 1 ? 'Semester 1 (Ganjil)' : 'Semester 2 (Genap)' }}" readonly>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <!--   DAFTAR ISI -->
            <div class="col-lg-7 col-md-12 bg-white p-3 shadow-sm">
                <h4 class="fw-bold mb-3">Daftar Isi: {{ $lesson->name }}</h4>
                <div class="text-center mt-3">
                    <button class="btn btn-add w-100 mb-3" data-bs-toggle="modal" data-bs-target="#importModal">
                        <i class="bi bi-upload"></i> Import File
                    </button>
                </div>

                <div id="lessonTree" class="scroll-area p-2">
                    @foreach ($lesson->themes as $theme)
                        <div class="p-2 mb-2 rounded border bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>Bab {{ $theme->theme }}:</strong> {{ $theme->name }}
                                </div>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-alt-1"
                                        onclick="editItem('theme', {{ $theme->id }})">Edit</button>
                                    <button class="btn btn-alt-2"
                                        onclick="deleteItem('theme', {{ $theme->id }})">Hapus</button>
                                </div>
                            </div>

                            {{-- SUBBAB --}}
                            <div class="ms-4 mt-2 ps-3 border-start border-3 border-secondary">
                                @foreach ($theme->subthemes as $sub)
                                    <div class="p-2 mb-1 rounded border bg-white">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <span>Subbab {{ $sub->subtheme }}:</span> {{ $sub->name }}
                                            </div>
                                            <div class="d-flex gap-2">
                                                <button class="btn btn-alt-1"
                                                    onclick="editItem('subtheme', {{ $sub->id }})">Edit</button>
                                                <button class="btn btn-alt-2"
                                                    onclick="deleteItem('subtheme', {{ $sub->id }})">Hapus</button>
                                            </div>
                                        </div>

                                        {{-- MATERI --}}
                                        <div class="ms-4 mt-2 ps-3 border-start border-2 border-light">
                                            @foreach ($sub->lessonItems as $item)
                                                <div class="p-2 mb-1 rounded border">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <span>Materi {{ $item->number }}:</span> {{ $item->title }}
                                                            <div class="text-start mt-1">
                                                                <small class="text-muted fst-italic">
                                                                    <i class="far fa-user"></i>
                                                                    {{ $item->admin->username ?? 'Tidak Diketahuin' }}
                                                                </small>

                                                                <small class="text-muted fst-italic">
                                                                    <i class="far fa-calendar"></i>
                                                                    {{ $item->created_at?->format('d M Y H:i') }}
                                                                </small>
                                                            </div>
                                                        </div>

                                                        <div class="d-flex gap-2">

                                                            <!-- TOMBOL PREVIEW -->
                                                            <button class="btn btn-add"
                                                                onclick="previewVideo('{{ base64_encode($item->embed) }}')">
                                                                Video
                                                            </button>


                                                            <button class="btn btn-alt-1"
                                                                onclick="editItem('item', {{ $item->id }})">Edit</button>

                                                            <button class="btn btn-alt-2"
                                                                onclick="deleteItem('item', {{ $item->id }})">Hapus</button>
                                                        </div>

                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
                <!-- Modal Preview -->
                <div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h5 class="modal-title">Preview Video</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body" id="previewContent">
                                <!-- iframe akan masuk ke sini -->
                            </div>

                        </div>
                    </div>
                </div>

                <!-- MODAL IMPORT -->
                <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content p-3">
                            <div class="modal-header border-0">
                                <h5 class="modal-title fw-bold" id="importModalLabel">Import Materi dari Excel</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('admin.pelajaran.import', ['lesson_id' => $lesson->id]) }}"
                                    method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="file" class="form-label fw-semibold">Pilih File Excel
                                            (.xlsx)</label>
                                        <input autocomplete="off" autocorrect="off" autocapitalize="off"
                                            spellcheck="false" type="file" name="file" id="file"
                                            class="form-control" required>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-4">
                                        <button type="submit" class="btn btn-add">
                                            <i class="bi bi-upload"></i> Upload & Import
                                        </button>
                                        <a href="{{ asset('template/template_import_materi.xlsx') }}"
                                            class="btn btn-alt-1">
                                            <i class="bi bi-download"></i> Download Template
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!--   FORM PANEL -->
            <div class="col-lg-5 col-md-12 bg-white p-3 shadow-sm" id="formPanel">
                <h4 id="formTitle" class="fw-bold mb-3">Tambah Bab</h4>

                <div class="btn-group w-100 mb-3">
                    <button class="btn btn-add" onclick="showForm('theme','add')">+ Bab</button>
                    <button class="btn btn-alt-1" onclick="showForm('subtheme','add')">+ Subbab</button>
                    <button class="btn btn-alt-2" onclick="showForm('item','add')">+ Materi</button>
                </div>

                <form id="formData">@csrf
                    <div id="formContent"></div>
                    <button type="submit" id="btnSimpan" class="btn btn-add" style="display:none;">Simpan</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const lessonId = {{ $lesson->id }};
        let currentType = 'theme',
            currentMode = 'add',
            currentId = null;

        function notifSuccess(msg) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: msg,
                timer: 1500,
                showConfirmButton: false
            });
        }

        function notifError(msg) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: msg || 'Terjadi kesalahan',
                confirmButtonText: 'Tutup'
            });
        }

        // ===== SHOW FORM (kembali ke versi lama, kecuali nomor saat edit) =====
        function showForm(type, mode, data = {}) {
            const btnSimpan = document.getElementById("btnSimpan");
            const formContent = document.getElementById("formContent");
            btnSimpan.style.display = "inline-block";

            currentType = type;
            currentMode = mode;
            currentId = data.id || null;

            document.getElementById("formTitle").innerText =
                (mode === 'add' ? 'Tambah ' : 'Edit ') +
                (type === 'theme' ? 'Bab' : type === 'subtheme' ? 'Subbab' : 'Materi');

            // hapus helper hidden lama kalau ada (safety)
            const oldHelpers = formContent.querySelectorAll('.helper-hidden-input');
            oldHelpers.forEach(h => h.remove());

            let html = '';

            // === FORM BAB ===
            if (type === 'theme') {
                // Add: nomor hidden (auto). Edit: tampil input number
                if (mode === 'edit') {
                    html += `
                    <div class="mb-3">
                        <label>Nomor Bab</label>
                        <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" type="number" name="theme" class="form-control" value="${data.theme || ''}" required>
                    </div>`;
                } else {
                    html += `<input type="hidden" name="theme" value="">`;
                }

                html += `
                <div class="mb-3">
                    <label>Nama Bab</label>
                    <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" type="text" name="name" class="form-control" value="${data.name || ''}" required>
                </div>`;
            }

            // === FORM SUBBAB ===
            if (type === 'subtheme') {
                html += `
                <div class="mb-3">
                    <label>Pilih Bab</label>
                    <select name="theme_id" id="selectThemeSub" class="form-select" ${mode === 'edit' ? 'disabled' : ''} required>
                        <option value="">== Pilih ==</option>
                        @foreach ($lesson->themes as $t)
                            <option value="{{ $t->id }}">{{ 'Bab ' . $t->theme . ': ' . $t->name }}</option>
                        @endforeach
                    </select>
                </div>`;

                if (mode === 'edit') {
                    // nomor subbab tampil saat edit
                    html += `
                    <div class="mb-3">
                        <label>Nomor Subbab</label>
                        <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" type="number" name="subtheme" class="form-control" value="${data.subtheme || ''}" required>
                    </div>`;
                } else {
                    html += `<input type="hidden" name="subtheme" value="">`;
                }

                html += `
                <div class="mb-3">
                    <label>Nama Subbab</label>
                    <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" type="text" name="name" class="form-control" value="${data.name || ''}" required>
                </div>`;
            }

            // === FORM MATERI ===
            if (type === 'item') {
                html += `
    <input type="hidden" name="lesson_id" value="${lessonId}">
    <input type="hidden" name="admin_id" value="{{ Auth::id() }}">

    <div class="mb-3">
        <label class="visually-hidden">Pilih Bab</label>
        <select name="theme_id" id="selectThemeItem" class="form-select visually-hidden" ${mode === 'edit' ? 'disabled' : ''} required>
            <option value="">== Pilih ==</option>
            @foreach ($lesson->themes as $theme)
                <option value="{{ $theme->id }}">{{ 'Bab ' . $theme->theme . ': ' . $theme->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label>Pilih Subbab</label>
        <select name="subtheme_id" id="selectSubItem" class="form-select" ${mode === 'edit' ? 'disabled' : ''} required>
            <option value="">== Pilih ==</option>
            @foreach ($lesson->themes as $theme)
                @foreach ($theme->subthemes as $sub)
                    <option value="{{ $sub->id }}" data-theme="{{ $theme->id }}">
                        {{ 'Bab ' . $theme->theme . ' - Subbab ' . $sub->subtheme . ': ' . $sub->name }}
                    </option>
                @endforeach
            @endforeach
        </select>
    </div>
    `;

                // === Nomor Materi sekarang muncul DI BAWAH Subbab ===
                if (mode === 'edit') {
                    html += `
        <div class="mb-3">
            <label>Nomor Materi</label>
            <input type="number" name="number" class="form-control" value="${data.number || ''}" required>
        </div>`;
                } else {
                    html += `<input type="hidden" name="number" value="">`;
                }

                html += `
    <div class="mb-3">
        <label>Judul Materi</label>
        <input type="text" name="title" class="form-control" value="${data.title || ''}" required>
    </div>

    <div class="mb-3">
        <label>Embed / Link</label>
        <textarea name="embed" class="form-control" rows="3" required>${data.embed || ''}</textarea>
    </div>`;
            }

            if (mode === 'edit')
                html += `<input type="hidden" name="id" value="${data.id}">`;

            formContent.innerHTML = html;

            // SET VALUE & helper hidden agar select yang disabled tetap terkirim
            setTimeout(() => {
                if (type === 'subtheme' && data.theme_id) {
                    const selectThemeSub = document.getElementById('selectThemeSub');
                    if (selectThemeSub) {
                        selectThemeSub.value = data.theme_id;
                        if (selectThemeSub.disabled) addHiddenHelperInput('theme_id', selectThemeSub.value);
                    }
                }

                if (type === 'item') {
                    const selectThemeItem = document.getElementById('selectThemeItem');
                    const selectSubItem = document.getElementById('selectSubItem');

                    if (data.theme_id && selectThemeItem) {
                        selectThemeItem.value = data.theme_id;
                        if (selectThemeItem.disabled) addHiddenHelperInput('theme_id', selectThemeItem.value);
                    }
                    if (data.subtheme_id && selectSubItem) {
                        selectSubItem.value = data.subtheme_id;
                        if (selectSubItem.disabled) addHiddenHelperInput('subtheme_id', selectSubItem.value);
                    }

                    if (selectSubItem) {
                        selectSubItem.addEventListener('change', e => {
                            const selected = e.target.selectedOptions[0];
                            const themeId = selected.getAttribute('data-theme');
                            if (themeId && selectThemeItem) {
                                selectThemeItem.value = themeId;
                                updateHiddenHelperInput('theme_id', themeId);
                            }
                        });
                    }
                }
            }, 50);
        }

        // helper: tambahkan hidden input (class .helper-hidden-input supaya mudah dihapus)
        function addHiddenHelperInput(name, value) {
            const formContent = document.getElementById("formContent");
            // hapus jika sudah ada
            const exist = formContent.querySelector(`.helper-hidden-input[name="${name}"]`);
            if (exist) exist.remove();

            const inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = name;
            inp.value = value || '';
            inp.className = 'helper-hidden-input';
            formContent.appendChild(inp);
        }

        // helper: update value hidden input jika sudah ada
        function updateHiddenHelperInput(name, value) {
            const formContent = document.getElementById("formContent");
            const exist = formContent.querySelector(`.helper-hidden-input[name="${name}"]`);
            if (exist) exist.value = value || '';
        }

        // SUBMIT FORM
        document.getElementById('formData').addEventListener('submit', e => {
            e.preventDefault();
            const form = e.target;

            // Validasi dropdown: skip select yang disabled (karena helper hidden input kami buat)
            const selects = form.querySelectorAll('select[required]');
            for (let s of selects) {
                if (s.disabled) continue;
                if (!s.value) return notifError('Silakan pilih semua opsi yang tersedia!');
            }

            Swal.fire({
                title: currentMode === 'add' ? 'Simpan Data Baru?' : 'Simpan Perubahan?',
                icon: 'question',
                confirmButtonColor: '#B05B3B',
                cancelButtonColor: '#D79771',
                showCancelButton: true,
                confirmButtonText: 'Ya, Simpan',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then(result => {
                if (!result.isConfirmed) return;
                const fd = new FormData(form);
                const data = Object.fromEntries(fd.entries());
                const btn = form.querySelector('button[type="submit"]');
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';

                const method = currentMode === 'add' ? 'POST' : 'PUT';
                const url = currentMode === 'add' ?
                    `/admin/pelajaran/${lessonId}/materi/${currentType}` :
                    `/admin/pelajaran/${lessonId}/materi/${currentType}/${currentId}`;

                fetch(url, {
                        method,
                        headers: {
                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify(data)
                    })
                    .then(res => res.json())
                    .then(r => {
                        if (r.success) {
                            notifSuccess('Data berhasil disimpan!');
                            setTimeout(() => location.reload(), 800);
                        } else {
                            if (r.errors) {
                                const msgs = Object.values(r.errors).flat().join('\n');
                                notifError(msgs || r.message || 'Terjadi kesalahan');
                            } else {
                                notifError(r.message || 'Terjadi kesalahan');
                            }
                        }
                    })
                    .catch(err => notifError(err.message || 'Terjadi kesalahan jaringan'))
                    .finally(() => {
                        btn.disabled = false;
                        btn.innerHTML = 'Simpan';
                    });
            });
        });

        //   Tutup form jika klik di luar panel
        document.addEventListener("click", e => {
            if (!e.target.closest("#formPanel") && !e.target.closest(".btn-group")) {
                document.getElementById("formContent").innerHTML = "";
                document.getElementById("btnSimpan").style.display = "none";
                document.getElementById("formTitle").innerText = "Tambah Bab";
            }
        });

        //   EDIT
        function editItem(type, id) {
            fetch(`/admin/pelajaran/${lessonId}/materi/${type}/${id}/edit`)
                .then(res => res.json())
                .then(r => {
                    if (!r.success) return notifError('Data tidak ditemukan!');
                    showForm(type, 'edit', r.data);
                })
                .catch(() => notifError('Gagal mengambil data.'));
        }

        //   HAPUS
        function deleteItem(type, id) {
            Swal.fire({
                title: 'Hapus Data?',
                text: 'Data yang dihapus tidak bisa dikembalikan!',
                icon: 'warning',
                confirmButtonColor: '#B05B3B',
                cancelButtonColor: '#D79771',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then(result => {
                if (!result.isConfirmed) return;
                fetch(`/admin/pelajaran/${lessonId}/materi/${type}/${id}`, {
                        method: 'DELETE',
                        headers: {
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        }
                    })
                    .then(res => res.json())
                    .then(r => {
                        if (r.success) {
                            notifSuccess('Data berhasil dihapus!');
                            setTimeout(() => location.reload(), 800);
                        } else notifError(r.message);
                    })
                    .catch(() => notifError('Gagal menghapus data.'));
            });
        }

        function previewVideo(encodedEmbed) {
            let embed = atob(encodedEmbed);

            // Bungkus iframe dalam responsive container
            let wrapped = `
        <div class="video-wrapper">
            ${embed}
        </div>
    `;

            document.getElementById('previewContent').innerHTML = wrapped;

            let modal = new bootstrap.Modal(document.getElementById('previewModal'));
            modal.show();
        }
    </script>


@endsection
