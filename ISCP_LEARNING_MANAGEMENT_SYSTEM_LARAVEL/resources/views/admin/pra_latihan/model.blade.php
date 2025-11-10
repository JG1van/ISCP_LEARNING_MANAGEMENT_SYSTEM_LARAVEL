@extends('layouts.app')

@section('title', 'Manajemen Model Latihan')
@section('page_title', 'Manajemen Model Latihan')

@section('content')
    {{-- Pencarian & Tombol Tambah --}}
    <div class="row g-2 align-items-end mb-3">
        <div class="col-md-8">
            <label class="form-label">Pencarian</label>
            <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" id="searchInput" type="text"
                class="form-control" placeholder="Cari Nama Model Latihan..." />
        </div>
        <div class="col-md-4 text-end">
            <button class="btn btn-add w-100" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="fas fa-plus me-2"></i>Tambah Model Latihan
            </button>
        </div>
    </div>

    {{-- Tabel Model Latihan --}}
    <div class="table-responsive table-wrapper">
        <table class="table table-bordered w-100 table-hover text-center align-middle" id="modelTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Model Latihan</th>
                    <th style="width:180px;">Aksi</th>
                </tr>
            </thead>
            <tbody id="modelBody">
                @forelse ($data as $index => $item)
                    <tr id="row{{ $item->id }}">
                        <td>{{ $index + 1 }}</td>
                        <td class="model-name">{{ $item->name }}</td>
                        <td>
                            <div class="d-flex justify-content-center gap-1">
                                <button class="btn btn-sm-1" onclick="editModel('{{ $item->id }}')">Edit</button>
                                <button class="btn btn-sm-2"
                                    onclick="hapusModel('{{ $item->id }}', '{{ $item->name }}')">Hapus</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-muted text-center">Belum ada data model latihan.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3"></th>
                </tr>
        </table>
    </div>

    {{-- Modal Tambah --}}
    <div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="formTambah" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Model Latihan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">Nama Model Latihan</label>
                    <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" type="text"
                        name="name" class="form-control" placeholder="Masukkan nama model latihan" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-add w-100">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit --}}
    <div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="formEdit" class="modal-content">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Model Latihan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editId" name="id">
                    <label class="form-label">Nama Model Latihan</label>
                    <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" type="text"
                        id="editName" name="name" class="form-control" required>
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
        // === Notifikasi ===
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
            // === Pencarian ===
            const searchInput = document.getElementById("searchInput");
            searchInput.addEventListener("keyup", function() {
                const keyword = this.value.toLowerCase();
                document.querySelectorAll("#modelBody tr").forEach(row => {
                    const nama = row.querySelector(".model-name").textContent.toLowerCase();
                    row.style.display = nama.includes(keyword) ? "" : "none";
                });
            });

            // === Tambah ===
            document.getElementById("formTambah").addEventListener("submit", function(e) {
                e.preventDefault();
                const data = Object.fromEntries(new FormData(this));
                const btn = this.querySelector("button[type='submit']");
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';

                fetch("{{ route('admin.pra_latihan.model.store') }}", {
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

            // === Edit ===
            document.getElementById("formEdit").addEventListener("submit", function(e) {
                e.preventDefault();
                const id = document.getElementById("editId").value;
                const data = Object.fromEntries(new FormData(this));
                const btn = this.querySelector("button[type='submit']");
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';

                fetch(`/admin/pra_latihan/model/${id}`, {
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

        // === Ambil data untuk edit ===
        function editModel(id) {
            fetch(`/admin/pra_latihan/model/${id}/edit`)
                .then(res => res.json())
                .then(result => {
                    if (result.success) {
                        const l = result.data;
                        document.getElementById("editId").value = l.id;
                        document.getElementById("editName").value = l.name;
                        new bootstrap.Modal(document.getElementById("modalEdit")).show();
                    } else notifError(result.message);
                })
                .catch(err => notifError(err.message));
        }

        // === Hapus ===
        function hapusModel(id, nama) {
            Swal.fire({
                title: 'Hapus Model Latihan?',
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
                    fetch(`/admin/pra_latihan/model/${id}`, {
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
