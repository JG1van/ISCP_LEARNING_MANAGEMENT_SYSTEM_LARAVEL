@extends('layouts.app')

@section('title', 'Kompetensi Dasar')
@section('page_title')
    Manajemen Kompetensi Dasar (KD) - {{ $lesson->name }}
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
    </div>

    {{-- Tabel Kompetensi Dasar --}}
    <div class="shadow-sm p-3 rounded">
        <div class="row g-2 align-items-end mb-3">
            <div class="col-md-8">
                <label class="form-label">Pencarian</label>
                <input type="text" id="searchKD" class="form-control" placeholder="Cari KD...">
            </div>
            <div class="col-md-4 text-end">
                <button class="btn btn-add w-100" id="btnTambah" data-bs-toggle="modal" data-bs-target="#modalKD">
                    <i class="fas fa-plus me-2"></i>Tambah KD
                </button>
            </div>
        </div>

        <div class="table-responsive table-wrapper">
            <table class="table table-bordered w-100 table-hover text-center align-middle" id="lessonTable">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="15%">Kode</th>
                        <th>Deskripsi</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody id="kdBody">
                    @forelse ($competences as $index => $item)
                        <tr id="row{{ $item->id }}">
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->point }}</td>
                            <td class="kd-desc">{{ $item->description }}</td>
                            <td>
                                <div class="d-flex justify-content-center gap-1">
                                    <button class="btn btn-sm-1" onclick="editKD('{{ $item->id }}')">Edit</button>
                                    <button class="btn btn-sm-2"
                                        onclick="hapusKD('{{ $item->id }}', '{{ $item->point }}')">Hapus</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-muted text-center">Belum ada data KD.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Modal Tambah/Edit KD --}}
    <div class="modal fade" id="modalKD" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="formKD" class="modal-content">
                @csrf
                <input type="hidden" id="kdId" name="id">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah KD</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    <div id="kodeKDField" style="display: none;">
                        <label class="form-label">Poin KD</label>
                        <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" type="text"
                            id="point" name="point" class="form-control mb-3">
                    </div>

                    <label class="form-label">Deskripsi KD</label>
                    <textarea autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" id="description"
                        name="description" class="form-control" rows="4" required></textarea>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-add w-100">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    </div>
@endsection

@section('js')
    <script>
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

        const lessonId = "{{ $lesson->id }}";

        //   Realtime Search
        document.getElementById("searchKD").addEventListener("keyup", e => {
            const keyword = e.target.value.toLowerCase();
            document.querySelectorAll("#kdBody tr").forEach(row => {
                const desc = row.querySelector(".kd-desc")?.textContent.toLowerCase() || '';
                row.style.display = desc.includes(keyword) ? "" : "none";
            });
        });

        //   Simpan (Tambah / Edit) dengan Spinner
        document.getElementById("formKD").addEventListener("submit", async e => {
            e.preventDefault();

            const id = document.getElementById("kdId").value;
            const formData = new FormData(e.target);
            const method = id ? 'PUT' : 'POST';
            const url = id ?
                `/admin/pelajaran/${lessonId}/kd/${id}` :
                `/admin/pelajaran/${lessonId}/kd`;

            // tombol submit
            const btn = e.target.querySelector("button[type='submit']");
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';

            try {
                formData.append('_method', method);
                const res = await fetch(url, {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: formData
                });

                const result = await res.json();
                if (result.success) {
                    bootstrap.Modal.getInstance(document.getElementById('modalKD')).hide();
                    notifSuccess(result.message);
                    setTimeout(() => location.reload(), 1000);
                } else {
                    notifError(result.message);
                }
            } catch (err) {
                notifError("Terjadi kesalahan: " + err.message);
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        });

        //   Edit KD
        function editKD(id) {
            fetch(`/admin/pelajaran/${lessonId}/kd/${id}/edit`)
                .then(res => res.json())
                .then(result => {
                    if (result.success) {
                        document.getElementById("modalTitle").textContent = "Edit KD";
                        document.getElementById("kdId").value = result.data.id;
                        document.getElementById("description").value = result.data.description;
                        document.getElementById("point").value = result.data.point;
                        document.getElementById("kodeKDField").style.display = "block";
                        new bootstrap.Modal(document.getElementById('modalKD')).show();
                    } else {
                        notifError('Data KD tidak ditemukan.');
                    }
                })
                .catch(() => notifError('Gagal memuat data KD.'));
        }

        // ➕ Tambah KD
        document.getElementById("btnTambah").addEventListener("click", () => {
            document.getElementById("modalTitle").textContent = "Tambah KD";
            document.getElementById("kdId").value = "";
            document.getElementById("description").value = "";
            document.getElementById("point").value = "";
            document.getElementById("kodeKDField").style.display = "none";
        });

        // ❌ Hapus KD
        function hapusKD(id, kode) {
            Swal.fire({
                title: 'Hapus KD?',
                text: `Yakin ingin menghapus KD "${kode}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#B05B3B',
                cancelButtonColor: '#D79771',
                reverseButtons: true
            }).then(result => {
                if (result.isConfirmed) {
                    fetch(`/admin/pelajaran/${lessonId}/kd/${id}`, {
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
                            } else {
                                notifError(result.message);
                            }
                        })
                        .catch(() => notifError('Gagal menghapus data.'));
                }
            });
        }
    </script>
@endsection
