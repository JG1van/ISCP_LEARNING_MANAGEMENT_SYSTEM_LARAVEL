@extends('layouts.app')

@section('title', 'Riwayat Pengaduan')
@section('page_title', 'Riwayat Penyelesaian Pengaduan')

@section('content')

    {{--   FILTER --}}
    <div class=" mb-3">
        <form id="filterForm" class="row g-3">

            {{-- Tanggal --}}
            <div class="col-md-4">
                <label class="form-label">Tanggal</label>
                <input type="date" class="form-control" id="filterTanggal">
            </div>

            {{-- Kategori --}}
            <div class="col-md-4">
                <label class="form-label">Kategori</label>
                <select id="filterKategori" class="form-select">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Admin --}}
            <div class="col-md-4">
                <label class="form-label">Admin</label>
                <select id="filterAdmin" class="form-select">
                    <option value="">Semua Admin</option>
                    <option value="-">-</option>
                    @foreach ($admins as $adm)
                        <option value="{{ $adm->name }}">{{ $adm->name }}</option>
                    @endforeach
                </select>
            </div>

        </form>

    </div>

    {{--   TABEL RIWAYAT --}}
    <div class="table-responsive table-wrapper">
        <table class="table table-bordered table-hover align-middle text-center w-100" id="riwayatTable">
            <thead>
                <tr>
                    <th style="width:60px;">No</th>
                    <th>Waktu</th>
                    <th>Kategori</th>
                    <th>Admin</th>
                    <th>Penyelesaian Oleh</th>
                    <th style="width:150px;">Aksi</th>
                </tr>
            </thead>

            <tbody id="riwayatBody">
                @forelse ($data as $index => $item)
                    <tr id="row{{ $item->id }}">
                        <td>{{ $index + 1 }}</td>

                        <td>{{ \Carbon\Carbon::parse($item->completion_time)->format('d/m/Y H:i') }}</td>

                        <td>{{ $item->complaint_category->name ?? 'Belum memilih kategori' }}</td>

                        <td>{{ $item->admin->name ?? '-' }}</td>

                        <td>
                            @if ($item->resolution_by === 'Admin')
                                <span class="badge bg-info d-flex justify-content-center align-items-center">Admin</span>
                            @else
                                <span
                                    class="badge bg-primary d-flex justify-content-center align-items-center">Sistem</span>
                            @endif
                        </td>

                        <td>
                            <div class="d-flex justify-content-center gap-1">
                                {{-- Notes --}}
                                <button class="btn btn-sm-1"
                                    onclick="showNotes(`{{ $item->notes ? addslashes($item->notes) : 'Tidak ada catatan.' }}`)">
                                    Catatan
                                </button>

                                {{-- Hapus --}}
                                <button class="btn btn-sm-2" onclick="hapusRiwayat('{{ $item->id }}')">
                                    Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-muted">Belum ada riwayat pengaduan.</td>
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

@endsection

@section('js')

    {{--   NOTIFIKASI GLOBAL --}}
    <script>
        function notifSuccess(msg) {
            Swal.fire({
                icon: "success",
                title: "Berhasil!",
                text: msg,
                timer: 1800,
                showConfirmButton: false
            });
        }

        function notifError(msg) {
            Swal.fire({
                icon: "error",
                title: "Gagal!",
                text: msg,
                confirmButtonText: "Tutup"
            });
        }
    </script>

    {{--   MODAL CATATAN --}}
    <script>
        function showNotes(text) {
            Swal.fire({
                title: "Catatan Penyelesaian",
                html: `<div style='text-align:left'>${text.replace(/\n/g, "<br>")}</div>`,
                width: 600,
                confirmButtonText: "Tutup"
            });
        }
    </script>

    {{--   HAPUS RIWAYAT --}}
    <script>
        function hapusRiwayat(id) {
            Swal.fire({
                title: "Hapus Riwayat?",
                text: "Data riwayat akan dihapus permanen!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, Hapus",
                cancelButtonText: "Batal",
                reverseButtons: true,
                confirmButtonColor: "#B05B3B",
                cancelButtonColor: "#D79771",
            }).then(result => {
                if (result.isConfirmed) {

                    fetch(`/admin/pengaduan/riwayat/${id}/hapus`, {
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
                            } else {
                                notifError(result.message);
                            }
                        })
                        .catch(() => notifError("Terjadi kesalahan saat menghapus."));
                }
            });
        }
    </script>

    {{--   FILTER --}}
    <script>
        const filterTanggal = document.getElementById("filterTanggal");
        const filterKategori = document.getElementById("filterKategori");
        const filterAdmin = document.getElementById("filterAdmin");
        const rows = document.querySelectorAll("#riwayatBody tr");

        function applyFilter() {
            const tgl = filterTanggal.value;
            const kat = filterKategori.value.toLowerCase();
            const adm = filterAdmin.value.toLowerCase();

            rows.forEach(row => {
                const rowText = row.textContent.toLowerCase();
                const tanggalRow = row.children[1].textContent.trim().substring(0, 10)
                    .split('/').reverse().join('-');

                const matchTanggal = tgl ? tanggalRow === tgl : true;
                const matchKategori = kat ? rowText.includes(kat) : true;
                const matchAdmin = adm ? rowText.includes(adm) : true;

                row.style.display = (matchTanggal && matchKategori && matchAdmin) ? "" : "none";
            });
        }

        filterTanggal.addEventListener("change", applyFilter);
        filterKategori.addEventListener("change", applyFilter);
        filterAdmin.addEventListener("change", applyFilter);
    </script>

@endsection
