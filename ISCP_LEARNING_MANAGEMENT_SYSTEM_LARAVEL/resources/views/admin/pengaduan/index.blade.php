@extends('layouts.app')

@section('title', 'Daftar Pengaduan')
@section('page_title', 'Manajemen Pengaduan')

@section('content')

    {{-- 🔍 Pencarian --}}
    <div class="row g-2 align-items-end mb-3">
        <div class="col-md-8">
            <label class="form-label">Pencarian</label>
            <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" id="searchInput" type="text"
                class="form-control" placeholder="Cari nama pelapor, kode pengaduan, kategori..." />
        </div>
        <div class="col-md-4 text-end">
            <label class="form-label d-block">&nbsp;</label>
            <a href="{{ route('admin.pengaduan.riwayat') }}" class="btn btn-add w-100">
                Riwayat Pengaduan
            </a>
        </div>

    </div>

    {{-- 📄 Tabel Pengaduan --}}
    <div class="table-responsive table-wrapper">
        <table class="table table-bordered w-100 table-hover text-center align-middle" id="pengaduanTable">
            <thead>
                <tr>
                    <th style="width:60px;">No</th>
                    <th>Kode</th>
                    <th>Nama Pelapor</th>
                    <th>Kategori</th>
                    <th>Status</th>
                    <th style="width:150px;">Aksi</th>
                </tr>
            </thead>

            <tbody id="pengaduanBody">
                @forelse ($data as $index => $item)
                    <tr id="row{{ $item->id }}">
                        <td>{{ $index + 1 }}</td>

                        <td><b>{{ $item->complaint_code }}</b></td>

                        <td>
                            @if ($item->user)
                                {{ $item->user->name }}
                            @elseif ($item->student)
                                {{ $item->student->name }}
                            @else
                                Umum
                            @endif
                        </td>

                        <td>{{ $item->complaint_category->name ?? 'Belum memilih kategori' }}</td>

                        <td class="text-center align-middle">
                            @switch($item->chat_status)
                                @case('Admin')
                                    <span class="badge bg-info d-flex justify-content-center align-items-center">Admin</span>
                                @break

                                @default
                                    <span class="badge bg-primary d-flex justify-content-center align-items-center">Sistem</span>
                            @endswitch
                        </td>

                        <td>
                            <div class="d-flex justify-content-center gap-1">

                                {{-- Tombol Masuk --}}
                                <a href="{{ route('admin.pengaduan.ruang_pesan', $item->complaint_code) }}"
                                    class="btn btn-sm-1 {{ $item->chat_status !== 'Admin' ? 'disabled' : '' }}">
                                    Masuk
                                </a>

                                {{-- Tombol Hapus --}}
                                <button class="btn btn-sm-2"
                                    onclick="hapusPengaduan('{{ $item->complaint_code }}', '{{ $item->complaint_code }}')">
                                    Hapus
                                </button>
                            </div>
                        </td>
                    </tr>

                    @empty
                        <tr>
                            <td colspan="6" class="text-muted text-center">Belum ada pengaduan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    @endsection


    @section('js')

        {{-- 🔥 Notifikasi --}}
        <script>
            function notifSuccess(msg) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: msg,
                    timer: 1700,
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
        </script>


        {{-- 🔍 Pencarian --}}
        <script>
            document.getElementById("searchInput").addEventListener("keyup", function() {
                const keyword = this.value.toLowerCase();

                document.querySelectorAll("#pengaduanBody tr").forEach(row => {
                    row.style.display = row.textContent.toLowerCase().includes(keyword) ? "" : "none";
                });
            });
        </script>


        {{-- 🗑️ Hapus Pengaduan --}}
        <script>
            function hapusPengaduan(code, label) {
                Swal.fire({
                    title: 'Hapus Pengaduan?',
                    text: `Yakin ingin menghapus pengaduan "${label}"? Semua chat akan hilang.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    confirmButtonColor: '#B05B3B',
                    cancelButtonColor: '#D79771'
                }).then(result => {
                    if (!result.isConfirmed) return;

                    fetch(`/admin/pengaduan/${code}/hapus`, {
                            method: "DELETE",
                            headers: {
                                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                "Accept": "application/json"
                            }
                        })
                        .then(async res => {
                            // kalau bukan 2xx, bacanya tetap json (controller mengembalikan json)
                            const json = await res.json().catch(() => null);
                            if (!res.ok || !json) throw new Error(json?.message || 'Unknown error');
                            return json;
                        })
                        .then(result => {
                            if (result.success) {
                                // Remove row by ID returned from server
                                const row = document.getElementById(`row${result.id}`);
                                if (row) row.remove();
                                notifSuccess(result.message);
                            } else {
                                notifError(result.message || 'Gagal menghapus.');
                            }
                        })
                        .catch(err => {
                            console.error('delete error:', err);
                            notifError(err.message || 'Terjadi kesalahan saat menghapus.');
                        });
                });
            }
        </script>

    @endsection
