@extends('layouts.app')

@section('title', 'Manajemen Guru')
@section('page_title', 'Manajemen Guru')

@section('content')
    <div class="row g-2 align-items-end mb-3">
        <div class="col-md-8">
            <label class="form-label">Pencarian</label>
            <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" id="searchInput" type="text"
                class="form-control" placeholder="Cari Nama Guru...">
        </div>
        <div class="col-md-4 text-end">
            <button class="btn btn-add w-100" id="btnTambah" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="fas fa-plus me-2"></i>Tambah Guru
            </button>
        </div>
    </div>

    {{--   Tabel Guru --}}
    <div class="table-responsive table-wrapper">
        <table class="table table-bordered w-100 table-hover text-center align-middle" id="teacherTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Status</th>
                    <th style="width:220px;">Aksi</th>
                </tr>
            </thead>
            <tbody id="teacherBody">
                @forelse ($teachers as $index => $teacher)
                    <tr id="row{{ $teacher->id }}">
                        <td>{{ $index + 1 }}</td>
                        <td class="teacher-name">{{ $teacher->name }}</td>
                        <td>{{ $teacher->username }}</td>
                        <td>
                            @if ($teacher->role == 1)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-secondary">Tidak Aktif</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex justify-content-center gap-1">
                                <button class="btn btn-sm-1" onclick="editTeacher('{{ $teacher->id }}')">Datail /
                                    Edit</button>
                                <button class="btn btn-sm-2"
                                    onclick="hapusTeacher('{{ $teacher->id }}', '{{ $teacher->name }}')">Hapus</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-muted text-center">Belum ada data guru.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{--   Modal Tambah --}}
    <div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="formTambah" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Guru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body row g-3">
                    <div class="col-md-12">
                        <label class="form-label">Nama</label>
                        <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" type="text"
                            name="name" class="form-control" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Username</label>
                        <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" type="text"
                            name="username" class="form-control" required>
                    </div>
                    {{-- Password dihapus (akan diisi default dari backend) --}}
                    <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" type="hidden"
                        name="role" value="1">
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
                    <h5 class="modal-title fw-bold">Detail / Edit Guru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="row align-items-center g-4">
                        <!-- Kartu Guru -->
                        <div class="col-md-4 d-flex justify-content-center">
                            <div class="bg-light rounded-4 shadow-sm p-4 d-flex flex-column align-items-center justify-content-center"
                                style="height: 100%; min-height: 250px; position: relative;">

                                <!-- Foto Profil -->
                                <div class="position-relative">
                                    <img id="editImgPreview" src="{{ asset('images/logo.webp') }}" alt="Foto Guru"
                                        class="rounded-circle border shadow-sm bg-white" width="120" height="120"
                                        style="object-fit: cover;">

                                    <!-- Tombol Edit Pensil -->
                                    <button type="button"
                                        class="btn btn-sm btn-add rounded-circle position-absolute bottom-0 end-0 translate-middle shadow"
                                        id="btnEditPhoto"
                                        style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                </div>

                                <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                    type="file" id="editImgInput" name="photo" accept="image/*" hidden>

                                <h6 class="fw-bold mb-0 mt-3" id="editNameCard">Nama Guru</h6>
                            </div>
                        </div>

                        <!-- Detail Guru -->
                        <div class="col-md-8">
                            <div class="row g-3">
                                <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                    type="hidden" id="editId" name="id">

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nama</label>
                                    <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                        type="text" id="editName" name="name"
                                        class="form-control border-2 rounded-3" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Username</label>
                                    <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                        type="text" id="editUsername" name="username"
                                        class="form-control border-2 rounded-3" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Email</label>
                                    <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                        type="email" id="editEmail" name="email" class="form-control"
                                        autocomplete="new-email" autocorrect="off" autocapitalize="off"
                                        spellcheck="false" inputmode="email" class="form-control border-2 rounded-3">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">No. Telepon</label>
                                    <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                        type="number" id="editPhone" name="phone"
                                        class="form-control border-2 rounded-3">
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">Alamat</label>
                                    <textarea id="editAddress" name="address" class="form-control border-2 rounded-3" rows="2"></textarea>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Password</label>
                                    <div class="input autocomplete="off" autocorrect="off" autocapitalize="off"
                                        spellcheck="false"-group">
                                        <input autocomplete="off" autocorrect="off" autocapitalize="off"
                                            spellcheck="false" type="text" id="editPassword"
                                            class="form-control border-2 rounded-start-3" value="********" readonly>
                                        <button type="button" class="btn btn-outline-danger rounded-end-3 mt-2 w-100"
                                            id="btnResetPassword">
                                            <i class="fas fa-undo me-1"></i> Reset
                                        </button>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Status / Role</label>
                                    <select id="editRole" name="role" class="form-select border-2 rounded-3"
                                        required>
                                        <option value="1">Aktif</option>
                                        <option value="0">Tidak Aktif</option>
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

            <script>
                // Klik tombol pensil → buka file input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                document.getElementById('btnEditPhoto').addEventListener('click', function() {
                    document.getElementById('editImgInput').click();
                });

                // Preview gambar otomatis
                document.getElementById('editImgInput').addEventListener('change', function(event) {
                    const file = event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            document.getElementById('editImgPreview').src = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                });
            </script>

        </div>
    </div>




@endsection

@section('js')
    <script>
        //   Notifikasi
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
            //   Pencarian
            document.getElementById("searchInput").addEventListener("keyup", function() {
                const keyword = this.value.toLowerCase();
                document.querySelectorAll("#teacherBody tr").forEach(row => {
                    const nama = row.querySelector(".teacher-name").textContent.toLowerCase();
                    row.style.display = nama.includes(keyword) ? "" : "none";
                });
            });

            //   Reset form tambah setiap modal dibuka
            const modalTambah = document.getElementById('modalTambah');
            modalTambah.addEventListener('show.bs.modal', function() {
                document.getElementById('formTambah').reset();
            });

            //   Tambah Guru
            document.getElementById("formTambah").addEventListener("submit", async function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const btn = this.querySelector("button[type='submit']");
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';

                try {
                    const res = await fetch("{{ route('admin.guru.store') }}", {
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

            //   Edit Guru
            document.getElementById("formEdit").addEventListener("submit", async function(e) {
                e.preventDefault();
                const id = document.getElementById("editId").value;
                const formData = new FormData(this);
                const btn = this.querySelector("button[type='submit']");
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';

                try {
                    const res = await fetch(`/admin/guru/${id}`, {
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

            //   Tombol Reset Password
            document.getElementById("btnResetPassword").addEventListener("click", function() {
                const id = document.getElementById("editId").value;
                Swal.fire({
                    title: 'Reset Password?',
                    text: 'Password guru akan dikembalikan ke default.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Reset',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#B05B3B',
                    cancelButtonColor: '#D79771',
                    reverseButtons: true
                }).then(result => {
                    if (result.isConfirmed) {
                        fetch(`/admin/guru/${id}/reset-password`, {
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
        });

        //  Load data ke modal edit
        //   Edit Guru
        function editTeacher(id) {
            fetch(`/admin/guru/${id}/edit`)
                .then(res => res.json())
                .then(result => {
                    if (result.success) {
                        const t = result.data;

                        // Set field form
                        document.getElementById("editId").value = t.id;
                        document.getElementById("editName").value = t.name;
                        document.getElementById("editUsername").value = t.username;
                        document.getElementById("editEmail").value = t.email ?? '';
                        document.getElementById("editPhone").value = t.phone ?? '';
                        document.getElementById("editAddress").value = t.address ?? '';
                        document.getElementById("editRole").value = t.role;
                        document.getElementById("editPassword").value = "********";

                        // Nama & foto di kartu
                        document.getElementById("editNameCard").innerText = t.name ?? "Tanpa Nama";

                        // Ganti gambar profil (jika ada)
                        const imgPreview = document.getElementById("editImgPreview");
                        if (t.img) {
                            imgPreview.src = `/images/users/${t.img}`;
                        } else {
                            imgPreview.src = `/images/logo.webp`; // default
                        }




                        // Tampilkan modal
                        new bootstrap.Modal(document.getElementById("modalEdit")).show();
                    } else {
                        notifError('Data guru tidak ditemukan.');
                    }
                })
                .catch(() => notifError('Gagal memuat data guru.'));
        }

        //   Reset Password
        document.getElementById("btnResetPassword").addEventListener("click", function() {
            const id = document.getElementById("editId").value;
            Swal.fire({
                title: 'Reset Password?',
                text: 'Password akan direset ke default (Guru1234)',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Reset',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#B05B3B',
                cancelButtonColor: '#D79771',
                reverseButtons: true
            }).then(result => {
                if (result.isConfirmed) {
                    fetch(`/admin/guru/${id}/reset-password`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': "{{ csrf_token() }}"
                            }
                        })
                        .then(res => res.json())
                        .then(result => {
                            if (result.success) notifSuccess(result.message);
                            else notifError(result.message);
                        })
                        .catch(() => notifError('Terjadi kesalahan saat mereset password.'));
                }
            });
        });

        //   Hapus
        function hapusTeacher(id, nama) {
            Swal.fire({
                title: 'Hapus Guru?',
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
                    fetch(`/admin/guru/${id}`, {
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
