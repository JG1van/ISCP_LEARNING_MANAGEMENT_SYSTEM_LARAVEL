@extends('admin.layouts.app')

@section('title', 'Riwayat Serial')
@section('page_title', 'Riwayat Serial')

@section('content')

    {{-- 🔍 Pencarian & Filter --}}
    <div class="row g-2 mb-3">

        {{-- Pencarian --}}
        <div class="col-md-4">
            <label class="form-label">Cari Serial / Status</label>
            <input type="text" id="searchInput" class="form-control" placeholder="Cari serial, status..." autocomplete="off">
        </div>

        {{-- Filter Status --}}
        <div class="col-md-3">
            <label class="form-label">Filter Status</label>
            <select id="filterStatus" class="form-select">
                <option value="">Semua</option>
                <option value="Baru">Baru</option>
                <option value="Perpanjang">Perpanjang</option>
            </select>
        </div>

        {{-- Filter Tanggal --}}
        <div class="col-md-3">
            <label class="form-label">Filter Tanggal</label>
            <input type="date" id="filterTanggal" class="form-control">
        </div>

        {{-- Reset --}}
        <div class="col-md-2">
            <label class="form-label ">&nbsp;</label>
            <button id="resetFilter" class="btn btn-add w-100">Reset</button>
        </div>

    </div>

    {{-- Tabel --}}
    <div class="table-responsive table-wrapper mt-3">
        <table class="table table-bordered w-100 table-hover text-center align-middle" id="logTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Serial</th>
                    <th>Aktif (bulan)</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($logs as $i => $log)
                    <tr>
                        <td>{{ $i + 1 }}</td>

                        <td class="log-date">
                            {{ \Carbon\Carbon::parse($log->created_at)->format('Y-m-d H:i') }}
                        </td>


                        <td class="log-serial fw-bold">{{ $log->serial->serial ?? '-' }}</td>

                        <td>{{ $log->active }}</td>

                        <td class="text-center align-middle">
                            @if ($log->status == 'Baru')
                                <span class="badge bg-success d-flex justify-content-center align-items-center">Baru</span>
                            @elseif ($log->status == 'Perpanjang')
                                <span
                                    class="badge bg-warning text-dark d-flex justify-content-center align-items-center">Perpanjang</span>
                            @else
                                <span class="badge bg-secondary d-flex justify-content-center align-items-center">Tidak
                                    Terbaca</span>
                            @endif
                        </td>


                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-muted py-3">Belum ada riwayat serial.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="5"></th>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- Script Filter --}}
    <script>
        const searchInput = document.getElementById('searchInput');
        const filterStatus = document.getElementById('filterStatus');
        const filterTanggal = document.getElementById('filterTanggal');
        const resetBtn = document.getElementById('resetFilter');
        const rows = document.querySelectorAll('#logTable tbody tr');

        function filterTable() {
            const search = searchInput.value.toLowerCase();
            const status = filterStatus.value;
            const tanggal = filterTanggal.value;

            rows.forEach(row => {
                const serial = row.querySelector('.log-serial').textContent.toLowerCase();
                const rowStatus = row.querySelector('.log-status').textContent.trim();
                const rowDate = row.querySelector('.log-date').textContent.trim();

                let tampil = true;

                // Filter pencarian
                if (search && !serial.includes(search) && !rowStatus.toLowerCase().includes(search)) {
                    tampil = false;
                }

                // Filter status
                if (status && rowStatus !== status) {
                    tampil = false;
                }

                // Filter tanggal
                if (tanggal && !rowDate.includes(tanggal)) {
                    tampil = false;
                }

                row.style.display = tampil ? '' : 'none';
            });
        }

        searchInput.addEventListener('input', filterTable);
        filterStatus.addEventListener('change', filterTable);
        filterTanggal.addEventListener('change', filterTable);

        resetBtn.addEventListener('click', () => {
            searchInput.value = '';
            filterStatus.value = '';
            filterTanggal.value = '';
            filterTable();
        });
    </script>

@endsection
