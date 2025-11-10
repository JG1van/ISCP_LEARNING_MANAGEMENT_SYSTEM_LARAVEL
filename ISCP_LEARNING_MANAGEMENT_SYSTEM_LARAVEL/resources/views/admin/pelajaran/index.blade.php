@extends('layouts.app')

@section('title', 'Manajemen Pelajaran')
@section('page_title', 'Manajemen Pelajaran')

@section('content')
    {{--   Pencarian & Tombol Tambah --}}
    <div class="row g-2 align-items-end mb-3">
        <div class="col-md-8">
            <label class="form-label">Pencarian</label>
            <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" id="searchInput" type="text"
                class="form-control" placeholder="Cari Nama Pelajaran..." />
        </div>
        <div class="col-md-4 text-end">
            <button class="btn btn-add w-100" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="fas fa-plus me-2"></i>Tambah Pelajaran
            </button>
        </div>
    </div>

    {{--   Tabel Pelajaran --}}
    <div class="table-responsive table-wrapper">
        <table class="table table-bordered w-100 table-hover text-center align-middle" id="lessonTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Pelajaran</th>
                    <th>Mata Pelajaran</th>
                    <th>Kelas</th>
                    <th>Semester</th>
                    <th>Kategori</th>
                    <th style="width:180px;">Aksi</th>
                </tr>
            </thead>
            <tbody id="lessonBody">
                @forelse ($data as $index => $item)
                    <tr id="row{{ $item->id }}">
                        <td>{{ $index + 1 }}</td>
                        <td class="lesson-name">{{ $item->name }}</td>
                        <td>{{ $item->mapel->name ?? '-' }}</td>
                        <td>Kelas {{ $item->grade }}</td>
                        <td>{{ $item->semester == 1 ? 'Semester 1 (Ganjil)' : 'Semester 2 (Genap)' }}</td>
                        <td>
                            @if ($item->category == 1)
                                Teori Pelajaran
                            @elseif ($item->category == 2)
                                Latihan Soal
                            @else
                                <span class="text-muted">Tidak Diketahui</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex justify-content-center gap-1">
                                <button class="btn btn-add" onclick="openSelectModal('{{ $item->id }}')">
                                    Pilih
                                </button>
                                <button class="btn btn-sm-1" onclick="editLesson('{{ $item->id }}')">Edit</button>
                                <button class="btn btn-sm-2"
                                    onclick="hapusLesson('{{ $item->id }}', '{{ $item->name }}')">Hapus</button>
                            </div>

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-muted text-center">Belum ada data pelajaran.</td>
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
    <!-- Modal Pilihan -->
    <div class="modal fade" id="modalSelect" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pilih Aksi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <button id="btnMateri" class="btn btn-add w-100 mb-2">Materi Pelajaran</button>
                    <button id="btnKD" class="btn btn-sm-1 w-100 mb-2">Kompetensi Dasar</button>
                    <button id="btnSoal" class="btn btn-sm-2 w-100">Latihan Soal</button>
                </div>
            </div>
        </div>
    </div>

    {{--   Modal Tambah Pelajaran --}}
    <div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="formTambah" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Pelajaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Mata Pelajaran</label>
                        <select name="mapel_id" class="form-select" required>
                            <option value="">== Pilih ==</option>
                            @foreach ($mapels as $mapel)
                                <option value="{{ $mapel->id }}">{{ $mapel->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nama Pelajaran</label>
                        <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" type="text"
                            name="name" class="form-control" placeholder="Masukkan nama pelajaran" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Kelas</label>
                        <select name="grade" class="form-select" required>
                            <option value="">== Pilih ==</option>
                            @for ($i = 1; $i <= 6; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Semester</label>
                        <select name="semester" class="form-select" required>
                            <option value="">== Pilih ==</option>
                            <option value="1">1 (Ganjil)</option>
                            <option value="2">2 (Genap)</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Kategori</label>
                        <select name="category" class="form-select" required>
                            <option value="">== Pilih ==</option>
                            <option value="1">Teori Pelajaran</option>
                            <option value="2">Latihan Soal</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-add w-100">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{--   Modal Edit Pelajaran --}}
    <div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="formEdit" class="modal-content">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Pelajaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body row g-3">
                    <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" type="hidden"
                        id="editId" name="id">
                    <div class="col-md-6">
                        <label class="form-label">Mata Pelajaran</label>
                        <select id="editMapelId" name="mapel_id" class="form-select" required>
                            <option value="">== Pilih ==</option>
                            @foreach ($mapels as $mapel)
                                <option value="{{ $mapel->id }}">{{ $mapel->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nama Pelajaran</label>
                        <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                            type="text" id="editName" name="name" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Kelas</label>
                        <select id="editGrade" name="grade" class="form-select" required>
                            @for ($i = 1; $i <= 6; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Semester</label>
                        <select id="editSemester" name="semester" class="form-select" required>
                            <option value="1">1 (Ganjil)</option>
                            <option value="2">2 (Genap)</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Kategori</label>
                        <select id="editCategory" name="category" class="form-select" required>
                            <option value="1">Teori Pelajaran</option>
                            <option value="2">Latihan Soal</option>
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
        //   Notifikasi cepat
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
        let selectedLessonId = null;

        function openSelectModal(id) {
            selectedLessonId = id;
            new bootstrap.Modal(document.getElementById("modalSelect")).show();
        }

        document.getElementById("btnMateri").addEventListener("click", () => {
            window.location.href = `/admin/pelajaran/${selectedLessonId}/materi`;
        });

        document.getElementById("btnKD").addEventListener("click", () => {
            window.location.href = `/admin/pelajaran/${selectedLessonId}/kd`;
        });

        document.getElementById("btnSoal").addEventListener("click", () => {
            window.location.href = `/admin/pelajaran/${selectedLessonId}/latihan_soal`;
        });

        document.addEventListener("DOMContentLoaded", () => {

            //   Pencarian
            const searchInput = document.getElementById("searchInput");
            searchInput.addEventListener("keyup", function() {
                const keyword = this.value.toLowerCase();
                document.querySelectorAll("#lessonBody tr").forEach(row => {
                    const nama = row.querySelector(".lesson-name").textContent.toLowerCase();
                    row.style.display = nama.includes(keyword) ? "" : "none";
                });
            });

            //   Tambah Pelajaran
            document.getElementById("formTambah").addEventListener("submit", function(e) {
                e.preventDefault();

                const data = Object.fromEntries(new FormData(this));
                const btn = this.querySelector("button[type='submit']");
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';

                fetch("{{ route('admin.pelajaran.store') }}", {
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
                            this.reset();
                            notifSuccess(result.message);
                            setTimeout(() => location.reload(), 1000);
                        } else notifError(result.message);
                    })
                    .catch(err => notifError(err.message))
                    .finally(() => {
                        btn.disabled = false;
                        btn.innerHTML = 'Simpan';
                    });
            });

            //   Edit Pelajaran
            document.getElementById("formEdit").addEventListener("submit", function(e) {
                e.preventDefault();
                const id = document.getElementById("editId").value;
                const data = Object.fromEntries(new FormData(this));
                const btn = this.querySelector("button[type='submit']");
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';

                fetch(`/admin/pelajaran/${id}`, {
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
                            setTimeout(() => location.reload(), 1000);
                        } else notifError(result.message);
                    })
                    .catch(err => notifError(err.message))
                    .finally(() => {
                        btn.disabled = false;
                        btn.innerHTML = 'Simpan Perubahan';
                    });
            });
        });

        //   Edit Data
        function editLesson(id) {
            fetch(`/admin/pelajaran/${id}/edit`)
                .then(res => res.json())
                .then(result => {
                    if (result.success) {
                        const l = result.data;
                        document.getElementById("editId").value = l.id;
                        document.getElementById("editMapelId").value = l.mapel_id;
                        document.getElementById("editName").value = l.name;
                        document.getElementById("editGrade").value = l.grade;
                        document.getElementById("editSemester").value = l.semester;
                        document.getElementById("editCategory").value = l.category;
                        new bootstrap.Modal(document.getElementById("modalEdit")).show();
                    } else notifError(result.message);
                })
                .catch(err => notifError(err.message));
        }

        //   Hapus
        function hapusLesson(id, nama) {
            Swal.fire({
                title: 'Hapus Pelajaran?',
                text: `Yakin ingin menghapus "${nama}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                confirmButtonColor: '#B05B3B',
                cancelButtonColor: '#D79771'
            }).then(result => {
                if (result.isConfirmed) {
                    fetch(`/admin/pelajaran/${id}`, {
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
                        .catch(() => notifError('Terjadi kesalahan saat menghapus.'));
                }
            });
        }
    </script>
@endsection
