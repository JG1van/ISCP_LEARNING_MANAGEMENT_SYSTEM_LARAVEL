@extends('admin.layouts.app')

@section('title', 'Manajemen Siswa')
@section('page_title', 'Manajemen Siswa')

@section('content')
    {{--   Header Informasi Kelas --}}
    @if (isset($classroom))
        <div class="col-lg-12 col-md-12 bg-white p-3 shadow-sm mb-3 rounded">
            <h5 class="fw-bold mb-3">Informasi Kelas</h5>
            <div class="row g-3">
                <div class="col-md-12">
                    <label class="form-label fw-semibold">Nama Kelas</label>
                    <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" type="text"
                        class="form-control" value="{{ $classroom->name ?? 'Belum ditentukan' }}" readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Serial ID</label>
                    <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" type="text"
                        class="form-control" value="{{ $classroom->serial->name ?? '-' }}" readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Guru Pengajar</label>
                    <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" type="text"
                        class="form-control" value="{{ $classroom->serial->user->name ?? '-' }}" readonly>
                </div>
            </div>
        </div>
    @endif

    {{--   Pencarian dan Tombol Tambah --}}
    <div class="row g-2 align-items-end mb-3">
        <div class="col-md-8">
            <label class="form-label">Pencarian</label>
            <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" id="searchInput"
                type="text" class="form-control" placeholder="Cari Nama Siswa...">
        </div>
        <div class="col-md-4 text-end">
            <button class="btn btn-add w-100" id="btnPilihKelas" data-bs-toggle="modal" data-bs-target="#modalPilihKelas">
                <i class="fas fa-plus me-2"></i>Tambah Siswa
            </button>
        </div>
    </div>

    {{--   Tabel Siswa --}}
    <div class="table-responsive table-wrapper">
        <table class="table table-bordered w-100 table-hover text-center align-middle" id="studentTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Nama Kelas</th>
                    <th>Kode Serial dipakai</th>
                    <th>Guru Pengajar</th>
                    <th style="width:170px;">Aksi</th>
                </tr>
            </thead>
            <tbody id="studentBody">
                @forelse ($students as $index => $student)
                    <tr id="row{{ $student->id }}">
                        <td>{{ $index + 1 }}</td>
                        <td class="student-name">{{ $student->name }}</td>
                        <td>{{ $student->username }}</td>
                        <td>{{ $student->classroom->name ?? '-' }}</td>
                        <td>{{ $student->serial->serial ?? '-' }}</td>
                        <td>{{ $student->user->name ?? '-' }}</td>
                        <td>
                            <div class="d-flex justify-content-center gap-1">
                                <button class="btn btn-alt-1" onclick="editStudent('{{ $student->id }}')">Detail /
                                    Edit</button>
                                <button class="btn btn-alt-2"
                                    onclick="hapusStudent('{{ $student->id }}', '{{ $student->name }}')">Hapus</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-muted text-center">Belum ada data siswa.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="7"></th>
                </tr>
            </tfoot>
        </table>
    </div>

    {{--   Modal Pilih Kelas --}}
    <div class="modal fade" id="modalPilihKelas" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="formPilihKelas" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Pilih Kelas untuk Tambah Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label fw-semibold">Pilih Kelas</label>
                    <select name="classroom_id" id="selectClassroom" class="form-select" required>
                        <option value="">-- Pilih Kelas --</option>
                        @foreach ($classrooms as $class)
                            <option value="{{ $class->id }}">
                                {{ $class->name }} - (Guru: {{ $class->serial->user->name ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-add w-100">Lanjutkan</button>
                </div>
            </form>
        </div>
    </div>

    {{--   Modal Edit --}}
    <div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <form id="formEdit" class="modal-content">
                @csrf
                @method('PUT')
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold">Detail / Edit Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row g-3">
                    <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" type="hidden"
                        id="editId" name="id">
                    <div class="col-md-12">
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
                        <label class="form-label fw-semibold">NIS</label>
                        <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                            type="text" id="editNis" name="nis" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" id="editEmail" name="email" class="form-control"
                            autocomplete="new-email" autocorrect="off" autocapitalize="off" spellcheck="false"
                            inputmode="email" type="email" id="editEmail" name="email" class="form-control">

                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Telepon</label>
                        <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                            type="text" id="editPhone" name="phone" class="form-control">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Password</label>
                        <div class="input-group">
                            <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                type="text" id="editPassword" class="form-control" value="********" readonly>
                            <button type="button" class="btn btn-alt-1" id="btnResetPassword">
                                <i class="fas fa-undo me-1"></i>Reset
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="submit" class="btn btn-add w-100 rounded-3">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('js')
    <script>
        //   Fungsi Notifikasi
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

        //   Filter pencarian
        document.getElementById("searchInput").addEventListener("keyup", function() {
            const keyword = this.value.toLowerCase();
            document.querySelectorAll("#studentBody tr").forEach(row => {
                const nama = row.querySelector(".student-name").textContent.toLowerCase();
                row.style.display = nama.includes(keyword) ? "" : "none";
            });
        });

        //  Pilih kelas sebelum tambah
        document.getElementById("formPilihKelas").addEventListener("submit", function(e) {
            e.preventDefault();
            const id = document.getElementById("selectClassroom").value;
            if (id) window.location.href = `siswa/create?classroom_id=${id}`;
        });

        //   Edit siswa
        function editStudent(id) {
            fetch(`/admin/siswa/${id}/edit`)
                .then(res => res.json())
                .then(result => {
                    if (result.success) {
                        const s = result.data;
                        document.getElementById("editId").value = s.id;
                        document.getElementById("editName").value = s.name;
                        document.getElementById("editUsername").value = s.username;
                        document.getElementById("editNis").value = s.nis ?? '';
                        document.getElementById("editEmail").value = s.email ?? '';
                        document.getElementById("editPhone").value = s.phone ?? '';
                        new bootstrap.Modal(document.getElementById("modalEdit")).show();
                    } else notifError('Data siswa tidak ditemukan.');
                })
                .catch(() => notifError('Gagal memuat data siswa.'));
        }

        //   Simpan Edit
        document.getElementById("formEdit").addEventListener("submit", async function(e) {
            e.preventDefault();
            const id = document.getElementById("editId").value;
            const formData = new FormData(this);
            try {
                const res = await fetch(`/admin/siswa/${id}`, {
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
                } else notifError(result.message);
            } catch (err) {
                notifError(err.message);
            }
        });

        //   Reset Password
        document.getElementById("btnResetPassword").addEventListener("click", function() {
            const id = document.getElementById("editId").value;
            Swal.fire({
                title: 'Reset Password?',
                text: 'Password akan dikembalikan ke default (Siswa1234)',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Reset',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#B05B3B',
                cancelButtonColor: '#D79771',
                reverseButtons: true
            }).then(result => {
                if (result.isConfirmed) {
                    fetch(`/admin/siswa/${id}/reset-password`, {
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

        //   Hapus
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
                                document.getElementById(`row${id}`).remove();
                                notifSuccess(result.message);
                            } else notifError(result.message);
                        })
                        .catch(() => notifError('Gagal menghapus.'));
                }
            });
        }
    </script>
@endsection
