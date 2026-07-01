@extends('admin.layouts.app')

@section('title', 'Daftar Layanan Pelanggan ')
@section('page_title', 'Manajemen Layanan Pelanggan ')

@section('content')

    {{--   Pencarian --}}
    <div class="row g-2 align-items-end mb-3">
        <div class="col-md-8">
            <label class="form-label">Pencarian</label>
            <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" id="searchInput" type="text"
                class="form-control" placeholder="Cari nama pelapor, kode ruangan, kategori..." />
        </div>
        <div class="col-md-4 text-end">
            <label class="form-label d-block">&nbsp;</label>
            <a href="{{ route('admin.layanan-pelanggan.riwayat') }}" class="btn btn-add w-100"> <i
                    class="fas fa-history me-2"></i>
                Riwayat Percakapan
            </a>
        </div>

    </div>

    {{--   Tabel --}}
    <div class="table-responsive table-wrapper">
        <table class="table table-bordered w-100 table-hover text-center align-middle" id="pengaduanTable">
            <thead>
                <tr>
                    <th style="width:60px;">No</th>
                    <th>Waktu</th>
                    <th>Kode Ruangan</th>
                    <th>Nama Pelapor</th>
                    <th>Kategori</th>
                    <th>Status</th>
                    <th style="width:250px;">Aksi</th>
                </tr>
            </thead>

            <tbody id="pengaduanBody">
                @forelse ($data as $index => $item)
                    <tr id="row{{ $item->id }}">
                        <td>{{ $index + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->updated_at)->format('d/m/Y H:i') }}</td>

                        <td><b>{{ $item->room_code }}</b></td>

                        <td>
                            @if ($item->user)
                                {{ $item->user->name }}
                            @elseif ($item->student)
                                {{ $item->student->name }}
                            @else
                                Umum
                            @endif
                        </td>

                        <td>{{ $item->question_category->name ?? 'Belum memilih kategori' }}</td>

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
                            @php
                                $updated = \Carbon\Carbon::parse($item->updated_at);
                                $hapusPada = $updated->copy()->addDays(7);

                                // Boleh hapus jika waktu sekarang sudah lewat 7 hari
                                $bolehHapus = now()->gte($hapusPada);

                                // Hitung sisa hari dari sekarang menuju 7 hari
                                $sisaHari = now()->diffInDays($hapusPada, false);

                                // Teks singkat
                                $teksSisa = $sisaHari > 0 ? $sisaHari . ' hari' : '';
                            @endphp


                            <div class="d-flex justify-content-center gap-1">

                                {{-- Tombol Masuk --}}
                                {{-- Tombol Masuk --}}
                                @if ($item->chat_status === 'Admin')
                                    <a href="{{ route('admin.layanan-pelanggan.ruang_pesan', $item->room_code) }}"
                                        class="btn btn-alt-1" style="width:150px;">
                                        Masuk Ruang
                                    </a>
                                @else
                                    <button class="btn btn-alt-1"style="width:150px;" disabled>
                                        Menunggu Status
                                    </button>
                                @endif


                                {{-- Tombol Hapus --}}
                                @if ($bolehHapus)
                                    <button class="btn btn-alt-2" style="width:100px;"
                                        onclick="hapusPengaduan('{{ $item->room_code }}', '{{ $item->room_code }}')">
                                        Hapus
                                    </button>
                                @else
                                    <button class="btn btn-alt-2" style="width:100px;" disabled>
                                        {{ $teksSisa }}
                                    </button>
                                @endif

                            </div>

                        </td>
                    </tr>

                    @empty
                        <tr>
                            <td colspan="7" class="text-muted text-center">Belum ada Layanan Pelanggan.</td>
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

    @endsection


    @section('js')

        {{--   Notifikasi --}}
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


        {{--   Pencarian --}}
        <script>
            document.getElementById("searchInput").addEventListener("keyup", function() {
                const keyword = this.value.toLowerCase();

                document.querySelectorAll("#pengaduanBody tr").forEach(row => {
                    row.style.display = row.textContent.toLowerCase().includes(keyword) ? "" : "none";
                });
            });
        </script>


        {{--   Hapus --}}
        <script>
            function hapusPengaduan(code, label) {
                Swal.fire({
                    title: 'Hapus Ruang Layanan Pelanggan?',
                    text: `Yakin ingin menghapus ruang layanan pelanggan "${label}"? Semua chat akan hilang.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    confirmButtonColor: '#B05B3B',
                    cancelButtonColor: '#D79771'
                }).then(result => {
                    if (!result.isConfirmed) return;

                    fetch(`/admin/layanan-pelanggan-admin/${code}/hapus`, {
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
