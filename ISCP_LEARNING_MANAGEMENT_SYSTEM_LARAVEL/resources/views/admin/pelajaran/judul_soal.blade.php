@extends('admin.layouts.app')

@section('title', 'Judul Soal')
@section('page_title')
    Manajemen Judul Soal - {{ $lesson->name }}
@endsection

@section('content')
    <div class="container-fluid py-3">

        {{-- Informasi Pelajaran --}}
        <div class="row g-3 bg-white p-3 shadow-sm mb-3 rounded">
            <h5 class="fw-bold mb-3">Informasi Pelajaran</h5>
            <div class="col-md-12">
                <label class="form-label fw-semibold">Nama Pelajaran</label>
                <input type="text" class="form-control" value="{{ $lesson->name }}" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Kelas</label>
                <input type="text" class="form-control" value="{{ $lesson->grade }}" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Semester</label>
                <input type="text" class="form-control" value="{{ $lesson->semester }}" readonly>
            </div>
        </div>

        {{--   Pencarian & Tambah --}}
        <div class="row g-2 align-items-end mb-3">
            <div class="col-md-8">
                <label class="form-label">Pencarian</label>
                <input id="searchInput" type="text" class="form-control" placeholder="Cari judul soal...">
            </div>
            <div class="col-md-4 text-end">
                <button class="btn btn-add w-100" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="fas fa-plus me-2"></i>Tambah Judul
                </button>
            </div>
        </div>

        {{-- 📋 Tabel Soal --}}
        <div class="table-responsive table-wrapper">
            <table class="table table-bordered table-hover text-center align-middle w-100" id="exerciseTable">
                <thead>
                    <tr>
                        <th style="width:50px;">No</th>
                        <th>Judul</th>
                        <th>Tipe Soal</th>
                        <th>Serial</th>
                        <th style="width:200px;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="exerciseBody">
                    @forelse ($data as $index => $exercise)
                        <tr id="row{{ $exercise->id }}">
                            <td>{{ $index + 1 }}</td>
                            <td class="exercise-title">{{ $exercise->title ?? '-' }}</td>
                            <td>{{ $exercise->exercise_type->name ?? '-' }}</td>
                            <td>
                                @if ($exercise->serial)
                                    {{ $exercise->serial->serial }}
                                @else
                                    <span class="text-muted fst-italic">Belum Ditentukan</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2 flex-wrap">
                                    {{-- Tombol ke daftar soal --}}
                                    <a href="{{ route('admin.pelajaran.judul_soal.soal.index', ['lesson_id' => $lesson->id, 'exercise_id' => $exercise->id]) }}"
                                        class="btn btn-add">Soal</a>

                                    {{-- Tombol Edit --}}
                                    <button class="btn btn-alt-2"
                                        onclick="editExercise('{{ $exercise->id }}')">Edit</button>

                                    {{-- Tombol Hapus --}}
                                    <button class="btn btn-alt-2"
                                        onclick="hapusExercise('{{ $exercise->id }}', '{{ $exercise->title }}')">Hapus</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-muted text-center py-4">Belum ada soal.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- 🟩 Modal Tambah --}}
    <div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="formTambah" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Soal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row g-3">
                    <div class="col-md-12">
                        <label class="form-label">Judul Soal</label>
                        <input type="text" name="title" class="form-control" placeholder="Masukkan judul soal"
                            required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tipe Soal</label>
                        <select name="exercise_type_id" class="form-select" required>
                            <option value="">== Pilih ==</option>
                            @foreach ($types as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Serial</label>
                        <select name="serial_id" class="form-select">
                            <option value="">-- Tidak ada --</option>
                            @foreach ($serials as $serial)
                                <option value="{{ $serial->id }}">{{ $serial->serial }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-add w-100">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- 🟦 Modal Edit --}}
    <div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="formEdit" class="modal-content">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Soal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row g-3">
                    <input type="hidden" id="editId" name="id">
                    <div class="col-md-12">
                        <label class="form-label">Judul Soal</label>
                        <input type="text" id="editTitle" name="title" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tipe Soal</label>
                        <select id="editType" name="exercise_type_id" class="form-select" required>
                            @foreach ($types as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Serial</label>
                        <select id="editSerial" name="serial_id" class="form-select">
                            <option value="">-- Tidak ada --</option>
                            @foreach ($serials as $serial)
                                <option value="{{ $serial->id }}">{{ $serial->serial }}</option>
                            @endforeach
                        </select>
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
        const lessonId = "{{ $lesson->id }}";

        function notifSuccess(msg) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: msg,
                timer: 1800,
                showConfirmButton: false
            });
        }

        function notifError(msg) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: msg,
                confirmButtonText: 'Tutup'
            });
        }

        //   Pencarian
        document.getElementById("searchInput").addEventListener("keyup", function() {
            const keyword = this.value.toLowerCase();
            document.querySelectorAll("#exerciseBody tr").forEach(row => {
                const title = row.querySelector(".exercise-title").textContent.toLowerCase();
                row.style.display = title.includes(keyword) ? "" : "none";
            });
        });

        //   Tambah
        document.getElementById("formTambah").addEventListener("submit", function(e) {
            e.preventDefault();

            const data = Object.fromEntries(new FormData(this));
            const btn = this.querySelector("button[type='submit']");
            const originalText = btn.innerHTML;

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';

            fetch(`/admin/pelajaran/${lessonId}/judul_soal`, {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(data)
                })
                .then(res => res.json())
                .then(result => {
                    if (result.success) {
                        bootstrap.Modal.getInstance(document.getElementById("modalTambah")).hide();
                        notifSuccess(result.message);
                        setTimeout(() => location.reload(), 800);
                    } else {
                        notifError(result.message);
                    }
                })
                .catch(err => notifError("Terjadi kesalahan: " + err.message))
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                });
        });

        // 🟡 Edit
        function editExercise(id) {
            fetch(`/admin/pelajaran/${lessonId}/judul_soal/${id}/edit`, {
                    headers: {
                        "Accept": "application/json"
                    }
                })
                .then(res => res.json())
                .then(result => {
                    if (result.success) {
                        const e = result.data;
                        document.getElementById("editId").value = e.id;
                        document.getElementById("editTitle").value = e.title;
                        document.getElementById("editType").value = e.exercise_type_id;
                        document.getElementById("editSerial").value = e.serial_id ?? '';
                        new bootstrap.Modal(document.getElementById("modalEdit")).show();
                    } else notifError(result.message);
                })
                .catch(err => notifError("Gagal memuat data: " + err.message));
        }

        // 🔧 Simpan Perubahan (Edit)
        document.getElementById("formEdit").addEventListener("submit", function(e) {
            e.preventDefault();

            const exerciseId = document.getElementById("editId").value;
            const data = Object.fromEntries(new FormData(this));
            const btn = this.querySelector("button[type='submit']");
            const originalText = btn.innerHTML;

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';

            fetch(`/admin/pelajaran/${lessonId}/judul_soal/${exerciseId}`, {
                    method: "PUT",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(data)
                })
                .then(res => res.json())
                .then(result => {
                    if (result.success) {
                        bootstrap.Modal.getInstance(document.getElementById("modalEdit")).hide();
                        notifSuccess(result.message);
                        setTimeout(() => location.reload(), 800);
                    } else {
                        notifError(result.message);
                    }
                })
                .catch(err => notifError("Terjadi kesalahan: " + err.message))
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                });
        });

        // 🔴 Hapus
        function hapusExercise(id, title) {
            Swal.fire({
                title: 'Hapus Soal?',
                text: `Yakin ingin menghapus "${title}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#B05B3B',
                cancelButtonColor: '#D79771'
            }).then(result => {
                if (result.isConfirmed) {
                    fetch(`/admin/pelajaran/${lessonId}/judul_soal/${id}`, {
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
                        .catch(() => notifError('Terjadi kesalahan.'));
                }
            });
        }
    </script>
@endsection
