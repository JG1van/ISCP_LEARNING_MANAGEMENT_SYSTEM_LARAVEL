@extends('layouts.app')

@section('title', 'Data Siswa')
@section('page_title', 'Data Siswa')

@section('content')
    @if (!$classroom->serial || !$classroom->serial->user)
        <div class="alert alert-warning text-center mt-4">
            <h5 class="fw-bold text-danger">⚠️ Data Tidak Lengkap</h5>
            <p>Harap tentukan <strong>Guru Pengampu (User)</strong> di bagian <strong>halaman Serial</strong> terlebih
                dahulu sebelum menambahkan siswa.</p>
            <a href="{{ route('admin.kelas.index') }}" class="btn btn-add mt-2">Kembali ke Data Kelas</a>
        </div>
    @else
        <div class="col-lg-12 col-md-12 bg-white p-3 shadow-sm mb-3 rounded">
            <h5 class="fw-bold mb-3">Informasi Kelas</h5>
            <div class="row g-3">
                <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" type="hidden"
                    id="classroom_id" value="{{ $classroom->id }}">
                <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" type="hidden"
                    id="serial_id" value="{{ $classroom->serial->id }}">
                <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" type="hidden"
                    id="teacher_id" value="{{ $classroom->serial->user->id }}">

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Nama Kelas</label>
                    <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" type="text"
                        class="form-control" value="{{ $classroom->name }}" readonly>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Kode Serial</label>
                    <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" type="text"
                        class="form-control" value="{{ $classroom->serial->serial }}" readonly>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Guru Pengajar</label>
                    <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" type="text"
                        class="form-control" value="{{ $classroom->serial->user->name }}" readonly>
                </div>
            </div>
        </div>

        <div class="row g-2 align-items-end mb-3">
            <div class="col-md-8">
                <label class="form-label">Pencarian</label>
                <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" id="searchInput"
                    type="text" class="form-control" placeholder="Cari Nama Siswa...">
            </div>
            <div class="col-md-4 text-end">
                <button class="btn btn-add w-100" id="btnTambah" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="fas fa-plus me-2"></i>Tambah Siswa
                </button>
            </div>
        </div>

        <div class="table-responsive table-wrapper">
            <table class="table table-bordered w-100 table-hover text-center align-middle" id="studentTable">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>Nama</th>
                        <th>Username</th>
                        <th width="220">Aksi</th>
                    </tr>
                </thead>
                <tbody id="studentBody">
                    @forelse ($students as $index => $student)
                        <tr id="row{{ $student->id }}">
                            <td>{{ $index + 1 }}</td>
                            <td class="student-name">{{ $student->name }}</td>
                            <td>{{ $student->username }}</td>
                            <td>
                                <div class="d-flex justify-content-center gap-1">
                                    <button class="btn btn-sm-1" onclick="editStudent('{{ $student->id }}')">Datail /
                                        Edit</button>
                                    <button class="btn btn-sm-2"
                                        onclick="hapusStudent('{{ $student->id }}', '{{ $student->name }}')">Hapus</button>
                                </div>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-muted text-center">Belum ada data siswa.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="6"></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form id="formTambah" class="modal-content">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Siswa</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body row g-3">
                        <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                            type="hidden" name="classroom_id" value="{{ $classroom->id }}">
                        <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                            type="hidden" name="serial_id" value="{{ $classroom->serial->id }}">
                        <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                            type="hidden" name="user_id" value="{{ $classroom->serial->user->id }}">

                        <div class="col-md-12">
                            <label class="form-label">Nama</label>
                            <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                type="text" name="name" class="form-control" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Username </label>
                            <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                type="text" name="username" class="form-control" required>
                        </div>
                    </div>


                    <div class="modal-footer">
                        <button type="submit" class="btn btn-add w-100">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form id="formEdit" class="modal-content">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Detail / Edit Siswa</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" type="hidden"
                        name="serial_id" id="editSerialId" value="{{ $classroom->serial->id }}">
                    <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" type="hidden"
                        name="user_id" id="editUserId" value="{{ $classroom->serial->user->id }}">
                    <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" type="hidden"
                        name="classroom_id" id="editClassroomId" value="{{ $classroom->id }}">

                    <div class="modal-body row g-3">
                        <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                            type="hidden" id="editId" name="id">

                        <div class="col-md-12">
                            <label class="form-label">Nama</label>
                            <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                type="text" id="editName" name="name" class="form-control" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Username</label>
                            <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                type="text" id="editUsername" name="username" class="form-control" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Email</label>
                            <input type="email" id="editEmail" name="email" class="form-control"
                                autocomplete="new-email" autocorrect="off" autocapitalize="off" spellcheck="false"
                                inputmode="email">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">NIS</label>
                            <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                type="text" id="editNis" name="nis" class="form-control">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Telepon</label>
                            <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                type="text" id="editPhone" name="phone" class="form-control">
                        </div>

                        <div class="col-md-12">
                            <button type="button" id="btnResetPass" class="btn btn-outline-danger w-100 mt-2">
                                <i class="fas fa-key me-2"></i>Reset Password
                            </button>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-add w-100">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

    @endsection

    @section('js')
        <script>
            //   Fungsi Notifikasi
            const notifSuccess = msg => Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: msg,
                timer: 1800,
                showConfirmButton: false
            });
            const notifError = msg => Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: msg,
                confirmButtonText: 'Tutup'
            });

            document.addEventListener("DOMContentLoaded", () => {

                //   Pencarian siswa
                document.getElementById("searchInput")?.addEventListener("keyup", e => {
                    const keyword = e.target.value.toLowerCase();
                    document.querySelectorAll("#studentBody tr").forEach(row => {
                        const nama = row.querySelector(".student-name")?.textContent.toLowerCase() ||
                            '';
                        row.style.display = nama.includes(keyword) ? "" : "none";
                    });
                });

                //   Tambah siswa
                const formTambah = document.getElementById("formTambah");
                if (formTambah) {
                    formTambah.addEventListener("submit", async e => {
                        e.preventDefault();
                        const btn = e.target.querySelector("button[type='submit']");
                        const formData = new FormData(e.target);
                        const originalHTML = btn.innerHTML;

                        // Spinner aktif
                        btn.disabled = true;
                        btn.innerHTML =
                            `<span class="spinner-border spinner-border-sm me-2"></span> Menyimpan...`;

                        try {
                            const res = await fetch("{{ route('admin.siswa.store') }}", {
                                method: "POST",
                                headers: {
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                },
                                body: formData
                            });
                            const result = await res.json();

                            if (result.success) {
                                bootstrap.Modal.getInstance(document.getElementById('modalTambah')).hide();
                                notifSuccess(result.message);
                                setTimeout(() => location.reload(), 1000);
                            } else notifError(result.message);
                        } catch (err) {
                            notifError('Terjadi kesalahan: ' + err.message);
                        } finally {
                            btn.disabled = false;
                            btn.innerHTML = originalHTML;
                        }
                    });
                }

                //   Edit siswa
                const formEdit = document.getElementById("formEdit");
                if (formEdit) {
                    formEdit.addEventListener("submit", async e => {
                        e.preventDefault();
                        const id = document.getElementById("editId").value;
                        const formData = new FormData(e.target);
                        formData.append('_method', 'PUT');
                        const btn = e.target.querySelector("button[type='submit']");
                        const originalHTML = btn.innerHTML;

                        // Spinner aktif
                        btn.disabled = true;
                        btn.innerHTML =
                            `<span class="spinner-border spinner-border-sm me-2"></span> Menyimpan...`;

                        try {
                            const res = await fetch(`/admin/siswa/${id}`, {
                                method: "POST",
                                headers: {
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                },
                                body: formData
                            });
                            const result = await res.json();

                            if (result.success) {
                                bootstrap.Modal.getInstance(document.getElementById("modalEdit")).hide();
                                notifSuccess(result.message);
                                setTimeout(() => location.reload(), 1000);
                            } else notifError(result.message);
                        } catch (err) {
                            notifError('Terjadi kesalahan: ' + err.message);
                        } finally {
                            btn.disabled = false;
                            btn.innerHTML = originalHTML;
                        }
                    });
                }

                //   Reset password
                const btnReset = document.getElementById("btnResetPass");
                if (btnReset) {
                    btnReset.addEventListener("click", async () => {
                        const id = document.getElementById("editId").value;
                        Swal.fire({
                            title: 'Reset Password?',
                            text: "Password akan dikembalikan ke default (Siswa1234).",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Ya, Reset',
                            cancelButtonText: 'Batal',
                            confirmButtonColor: '#B05B3B',
                            cancelButtonColor: '#D79771',
                            reverseButtons: true
                        }).then(async result => {
                            if (result.isConfirmed) {
                                btnReset.disabled = true;
                                const originalHTML = btnReset.innerHTML;
                                btnReset.innerHTML =
                                    `<span class="spinner-border spinner-border-sm me-2"></span> Mereset...`;

                                try {
                                    const res = await fetch(
                                        `/admin/siswa/${id}/reset-password`, {
                                            method: "POST",
                                            headers: {
                                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                            }
                                        });
                                    const json = await res.json();
                                    json.success ? notifSuccess(json.message) : notifError(json
                                        .message);
                                } catch {
                                    notifError('Gagal mereset password.');
                                } finally {
                                    btnReset.disabled = false;
                                    btnReset.innerHTML = originalHTML;
                                }
                            }
                        });
                    });
                }
            });

            //   Load data ke modal edit
            function editStudent(id) {
                fetch(`/admin/siswa/${id}/edit`)
                    .then(res => res.json())
                    .then(result => {
                        if (result.success) {
                            const s = result.data;
                            document.getElementById("editId").value = s.id;
                            document.getElementById("editName").value = s.name;
                            document.getElementById("editUsername").value = s.username;
                            document.getElementById("editEmail").value = s.email ?? '';
                            document.getElementById("editPhone").value = s.phone ?? '';
                            document.getElementById("editNis").value = s.nis ?? '';
                            new bootstrap.Modal(document.getElementById("modalEdit")).show();
                        } else notifError('Data siswa tidak ditemukan.');
                    })
                    .catch(() => notifError('Gagal memuat data siswa.'));
            }

            //   Hapus siswa
            function hapusStudent(id, nama) {
                Swal.fire({
                    title: 'Hapus Siswa?',
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
                        fetch(`/admin/siswa/${id}`, {
                                method: "DELETE",
                                headers: {
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                }
                            })
                            .then(res => res.json())
                            .then(result => {
                                if (result.success) {
                                    document.getElementById(`row${id}`)?.remove();
                                    notifSuccess(result.message);
                                } else notifError(result.message);
                            })
                            .catch(() => notifError('Gagal menghapus data.'));
                    }
                });
            }
        </script>


    @endif
@endsection
