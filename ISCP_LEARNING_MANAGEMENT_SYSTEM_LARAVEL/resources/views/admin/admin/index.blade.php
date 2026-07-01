@extends('admin.layouts.app')

@section('title', 'Manajemen Admin')
@section('page_title', 'Manajemen Admin')

@section('content')
    <div class="row g-2 align-items-end mb-3">
        <div class="col-md-8">
            <label class="form-label">Pencarian</label>
            <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" autocomplete="off"
                autocorrect="off" autocapitalize="off" spellcheck="false" id="searchInput" type="text" class="form-control"
                placeholder="Cari Nama Admin...">
        </div>
        <div class="col-md-4 text-end">
            <button class="btn btn-add w-100" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="fas fa-plus me-2"></i>Tambah Admin
            </button>
        </div>
    </div>

    {{--   Tabel Admin --}}
    <div class="table-responsive table-wrapper">
        <table class="table table-bordered w-100 table-hover text-center align-middle" id="adminTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Role / Status</th>
                    <th style="width:150px;">Aksi</th>
                </tr>
            </thead>
            <tbody id="adminBody">
                @forelse ($admins as $index => $admin)
                    <tr id="row{{ $admin->id }}">
                        <td>{{ $admin->id }}</td>
                        <td class="admin-name">{{ $admin->name }}</td>
                        <td>{{ $admin->username }}</td>
                        <td class="text-center align-middle">
                            @switch($admin->role)
                                @case(1)
                                    <span class="badge bg-dark d-flex justify-content-center align-items-center">Super-Admin</span>
                                @break

                                @case(2)
                                    <span class="badge bg-primary d-flex justify-content-center align-items-center">Admin</span>
                                @break

                                @case(3)
                                    <span
                                        class="badge bg-success d-flex justify-content-center align-items-center">Operasional</span>
                                @break

                                @case(4)
                                    <span
                                        class="badge bg-warning text-dark d-flex justify-content-center align-items-center">Konten-Pembelajaran</span>
                                @break

                                @case(5)
                                    <span
                                        class="badge bg-info text-dark d-flex justify-content-center align-items-center">Layanan-Pengguna</span>
                                @break

                                @default
                                    <span class="badge bg-secondary d-flex justify-content-center align-items-center">Tidak
                                        Aktif</span>
                            @endswitch
                        </td>


                        <td>
                            <div class="d-flex justify-content-center gap-1">
                                @if (auth()->user()->id !== $admin->id)
                                    <button class="btn btn-alt-1" onclick="editAdmin('{{ $admin->id }}')">Edit</button>
                                    <button class="btn btn-alt-2"
                                        onclick="hapusAdmin('{{ $admin->id }}', '{{ $admin->name }}')">Hapus</button>
                                @else
                                    <span class="text-muted small fst-italic">Akun Anda</span>
                                @endif
                            </div>
                        </td>

                    </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-muted text-center">Belum ada data admin.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="5"></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{--   Modal Tambah --}}
        <div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form id="formTambah" class="modal-content">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Admin</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Nama</label>
                            <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                autocomplete="off" type="text" name="name" class="form-control" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Username</label>
                            <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                autocomplete="off" type="text" name="username" class="form-control" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Tanggal Masuk</label>
                            <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" type="date"
                                name="date_in" class="form-control" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Role</label>
                            <select name="role" class="form-select" required>
                                <option value="0" selected>— Pilih Role —</option>
                                <option value="1">Super-Admin</option>
                                <option value="2">Admin</option>
                                <option value="3">Operasional</option>
                                <option value="4">Konten-Pembelajaran</option>
                                <option value="5">Layanan-Pengguna</option>
                            </select>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-add w-100">Simpan</button>
                    </div>
                </form>
            </div>
        </div>


        {{--  Modal Edit --}}
        <div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <form id="formEdit" class="modal-content border-0 shadow-lg rounded-4 overflow-hidden"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="modal-header bg-light border-bottom-0">
                        <h5 class="modal-title fw-bold">Edit Admin</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body p-4">
                        <div class="row align-items-center g-4">
                            <div class="col-md-4 d-flex justify-content-center">
                                <div class="bg-light rounded-4 shadow-sm p-4 d-flex flex-column align-items-center justify-content-center"
                                    style="min-height: 250px;">
                                    <div class="position-relative">
                                        @php
                                            $editPhotoPath =
                                                $admin->img && Storage::disk('public')->exists('admins/' . $admin->img)
                                                    ? asset('storage/admins/' . $admin->img)
                                                    : asset('images/logo.webp');
                                        @endphp

                                        <img id="editImgPreview" src="{{ $editPhotoPath }}"
                                            class="rounded-circle border shadow-sm bg-white" width="120" height="120"
                                            style="object-fit: cover;">
                                        <button type="button"
                                            class="btn btn-sm btn-add rounded-circle position-absolute bottom-0 end-0 translate-middle"
                                            id="btnEditPhoto" style="width: 35px; height: 35px;">
                                            <i class="fas fa-pen"></i>
                                        </button>
                                    </div>

                                    <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                        type="file" id="editImgInput" name="photo" accept="image/*" hidden>
                                    <h6 class="fw-bold mb-0 mt-3" id="editNameCard">Nama Admin</h6>
                                </div>
                            </div>

                            <div class="col-md-8">
                                <div class="row g-3">
                                    <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                        type="hidden" id="editId" name="id">

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Nama</label>
                                        <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                            type="text" id="editName" name="name" class="form-control" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Username</label>
                                        <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                            type="text" id="editUsername" name="username" class="form-control" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Tanggal Masuk</label>
                                        <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                            type="date" id="editDateIn" name="date_in" class="form-control" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Jabatan / Posisi</label>
                                        <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                            type="text" id="editPosition" name="position" class="form-control">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">No. Telepon</label>
                                        <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                            type="number" id="editPhone" name="phone" class="form-control">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Login Terakhir</label>
                                        <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                            type="text" id="editLoginAt" name="login_at" class="form-control" readonly>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Password</label>
                                        <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                            type="text" id="editPassword" class="form-control" value="********" readonly>
                                        <button type="button" class="btn btn-outline-danger w-100 mt-2"
                                            id="btnResetPassword">
                                            <i class="fas fa-undo me-1"></i> Reset Password
                                        </button>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Role / Status</label>
                                        <select id="editRole" name="role" class="form-select" required>
                                            <option value="0">Tidak Aktif</option>
                                            <option value="1">Super-Admin</option>
                                            <option value="2">Admin</option>
                                            <option value="3">Operasional</option>
                                            <option value="4">Konten-Pembelajaran</option>
                                            <option value="5">Layanan-Pengguna</option>
                                        </select>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light border-0">
                        <button type="submit" class="btn btn-add w-100 rounded-3 py-2 fw-semibold">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endsection

    @section('js')
        <script>
            // ==== Notifikasi ====
            function notifSuccess(msg) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: msg,
                    timer: 1800,
                    showConfirmButton: false
                });
            }

            function notifError(msg) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: msg,
                    confirmButtonText: 'Tutup'
                });
            }

            document.addEventListener("DOMContentLoaded", () => {
                // ==== Pencarian ====
                document.getElementById("searchInput").addEventListener("keyup", function() {
                    const keyword = this.value.toLowerCase();
                    document.querySelectorAll("#adminBody tr").forEach(row => {
                        const nama = row.querySelector(".admin-name").textContent.toLowerCase();
                        row.style.display = nama.includes(keyword) ? "" : "none";
                    });
                });

                // ==== Reset form tambah ====
                const modalTambah = document.getElementById('modalTambah');
                modalTambah.addEventListener('show.bs.modal', function() {
                    document.getElementById('formTambah').reset();
                });

                // ==== Tambah Admin ====
                document.getElementById("formTambah").addEventListener("submit", async function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    const btn = this.querySelector("button[type='submit']");
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';

                    try {
                        const res = await fetch("{{ route('admin.admin.store') }}", {
                            method: "POST",
                            headers: {
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: formData
                        });
                        const result = await res.json();
                        if (result.success) {
                            bootstrap.Modal.getInstance(modalTambah).hide();
                            this.reset();
                            notifSuccess(result.message);
                            setTimeout(() => location.reload(), 1000);
                        } else notifError(result.message || 'Gagal menyimpan data.');
                    } catch (err) {
                        notifError(err.message);
                    } finally {
                        btn.disabled = false;
                        btn.innerHTML = 'Simpan';
                    }
                });

                // ==== Edit Admin ====
                document.getElementById("formEdit").addEventListener("submit", async function(e) {
                    e.preventDefault();
                    const id = document.getElementById("editId").value;
                    const formData = new FormData(this);
                    const btn = this.querySelector("button[type='submit']");
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';

                    try {
                        const res = await fetch(`/admin/admin/${id}`, {
                            method: "POST",
                            headers: {
                                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                "X-HTTP-Method-Override": "PUT"
                            },
                            body: formData
                        });
                        const result = await res.json();
                        if (result.success) {
                            bootstrap.Modal.getInstance(document.getElementById("modalEdit")).hide();
                            notifSuccess(result.message);
                            setTimeout(() => location.reload(), 1000);
                        } else notifError(result.message || 'Gagal memperbarui data.');
                    } catch (err) {
                        notifError(err.message);
                    } finally {
                        btn.disabled = false;
                        btn.innerHTML = 'Simpan Perubahan';
                    }
                });

                // ==== Reset Password ====
                document.getElementById("btnResetPassword").addEventListener("click", function() {
                    const id = document.getElementById("editId").value;
                    Swal.fire({
                        title: 'Reset Password?',
                        text: 'Password admin akan dikembalikan ke default (Admin1234).',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Reset',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#B05B3B',
                        cancelButtonColor: '#D79771',
                        reverseButtons: true
                    }).then(result => {
                        if (result.isConfirmed) {
                            fetch(`/admin/admin/${id}/reset-password`, {
                                    method: "POST",
                                    headers: {
                                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                    }
                                })
                                .then(res => res.json())
                                .then(result => {
                                    if (result.success) notifSuccess(result.message);
                                    else notifError(result.message);
                                })
                                .catch(() => notifError('Gagal mereset password.'));
                        }
                    });
                });

                // ==== Upload Foto ====
                const btnEditPhoto = document.getElementById("btnEditPhoto");
                const editImgInput = document.getElementById("editImgInput");
                const editImgPreview = document.getElementById("editImgPreview");

                btnEditPhoto.addEventListener("click", () => editImgInput.click());

                editImgInput.addEventListener("change", (e) => {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = (ev) => {
                            editImgPreview.src = ev.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                });
            });

            // ==== Load data ke modal edit ====
            function editAdmin(id) {
                fetch(`/admin/admin/${id}/edit`)
                    .then(res => res.json())
                    .then(result => {
                        if (result.success) {
                            const a = result.data;
                            document.getElementById("editId").value = a.id;
                            document.getElementById("editName").value = a.name;
                            document.getElementById("editUsername").value = a.username;
                            document.getElementById("editDateIn").value = a.date_in ?? '';
                            document.getElementById("editPosition").value = a.position ?? '';
                            document.getElementById("editPhone").value = a.phone ?? '';
                            document.getElementById("editLoginAt").value = a.login_at ?? '';
                            document.getElementById("editRole").value = a.role ?? 0;
                            document.getElementById("editNameCard").innerText = a.name ?? "Tanpa Nama";

                            const imgPreview = document.getElementById("editImgPreview");
                            imgPreview.src = a.img ? `/storage/admins/${a.img}` : `/images/logo.webp`;

                            new bootstrap.Modal(document.getElementById("modalEdit")).show();
                        } else {
                            notifError('Data admin tidak ditemukan.');
                        }
                    })
                    .catch(() => notifError('Gagal memuat data admin.'));
            }

            // ==== Hapus Admin ====
            function hapusAdmin(id, nama) {
                Swal.fire({
                    title: 'Hapus Admin?',
                    text: `Yakin ingin menghapus "${nama}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#B05B3B',
                    cancelButtonColor: '#D79771',
                    reverseButtons: true
                }).then(result => {
                    if (result.isConfirmed) {
                        fetch(`/admin/admin/${id}`, {
                                method: "DELETE",
                                headers: {
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                }
                            })
                            .then(res => res.json())
                            .then(result => {
                                if (result.success) {
                                    document.getElementById(`row${id}`).remove();
                                    notifSuccess(result.message);
                                } else notifError(result.message || 'Gagal menghapus data.');
                            })
                            .catch(() => notifError('Terjadi kesalahan saat menghapus.'));
                    }
                });
            }
        </script>


    @endsection
