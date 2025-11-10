@extends('layouts.app')

@section('title', 'Dashboard LMS')
@section('page_title', 'Dashboard Learning Management System')

@section('content')
    <div class="container my-4">

        {{-- Kartu sambutan --}}
        <div class="row mb-3 align-items-stretch g-2">
            <div class="col-md-8">
                <div class="card p-4 d-flex flex-column flex-md-row justify-content-between align-items-center border shadow-sm text-center"
                    style="height: 300px">
                    {{-- Gambar kiri --}}
                    <div class="d-none d-md-block px-2">
                        <img src="{{ asset('images/ilustrasi-selamat-datang-1.png') }}" alt="Ilustrasi Kiri"
                            style="height: 100px; max-width: 200px;">
                    </div>

                    {{-- Teks tengah --}}
                    <div class="flex-grow-1 px-3">
                        <h5 class="fw-bold mb-2">Halo, <span class="text-primary">{{ Auth::user()->name ?? 'Admin' }}</span>
                            👋</h5>
                        <p class="fs-6 text-muted mb-0">Selamat datang di</p>
                        <p class="fs-6 fw-semibold text-dark mb-0">Sistem Manajemen Pembelajaran (LMS)</p>
                    </div>

                    {{-- Gambar kanan --}}
                    <div class="d-none d-md-block px-2">
                        <img src="{{ asset('images/ilustrasi-selamat-datang-2.png') }}" alt="Ilustrasi Kanan"
                            style="height: 100px; max-width: 200px;">
                    </div>
                </div>
            </div>

            {{-- Statistik kecil --}}
            <div class="col-md-4">
                <div class="row g-2">
                    <div class="col-6">
                        <div class="card p-3 text-center border" style="min-height: 140px;">
                            <i class="bi bi-book text-primary" style="font-size: 2rem;"></i>
                            <h6 class="mt-2 text-truncate">Total Materi</h6>
                            <h4 class="fw-bold text-dark fs-5">42</h4>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card p-3 text-center border" style="min-height: 140px;">
                            <i class="bi bi-people text-success" style="font-size: 2rem;"></i>
                            <h6 class="mt-2 text-truncate">Siswa Aktif</h6>
                            <h4 class="fw-bold text-dark fs-5">128</h4>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card p-3 text-center border" style="min-height: 140px;">
                            <i class="bi bi-person-workspace text-info" style="font-size: 2rem;"></i>
                            <h6 class="mt-2 text-truncate">Guru</h6>
                            <h4 class="fw-bold text-dark fs-5">18</h4>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card p-3 text-center border" style="min-height: 140px;">
                            <i class="bi bi-journal-bookmark text-warning" style="font-size: 2rem;"></i>
                            <h6 class="mt-2 text-truncate">Kelas Aktif</h6>
                            <h4 class="fw-bold text-dark fs-5">6</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Statistik tambahan --}}
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="card p-3 border d-flex flex-row align-items-center" style="gap: 15px;">
                    <i class="bi bi-tv text-info" style="font-size: 2rem;"></i>
                    <div>
                        <div class="fw-semibold">Produk Pembelajaran</div>
                        <div class="fw-bold text-dark fs-6">10 Modul</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3 border d-flex flex-row align-items-center" style="gap: 15px;">
                    <i class="bi bi-collection text-primary" style="font-size: 2rem;"></i>
                    <div>
                        <div class="fw-semibold">Tema & Subtema</div>
                        <div class="fw-bold text-dark fs-6">15 Tema</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3 border d-flex flex-row align-items-center" style="gap: 15px;">
                    <i class="bi bi-key text-success" style="font-size: 2rem;"></i>
                    <div>
                        <div class="fw-semibold">Serial Aktif</div>
                        <div class="fw-bold text-dark fs-6">24 Kode</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Grafik Placeholder --}}
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="card p-3 border">
                    <h6 class="text-center"><i class="bi bi-pie-chart"></i> Distribusi Materi per Mapel</h6>
                    <div style="height: 250px;">
                        <canvas id="materiChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card p-3 border">
                    <h6 class="text-center"><i class="bi bi-bar-chart"></i> Jumlah Siswa per Kelas</h6>
                    <div style="height: 250px;">
                        <canvas id="kelasChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        const materiChart = new Chart(document.getElementById('materiChart'), {
            type: 'doughnut',
            data: {
                labels: ['Matematika', 'IPA', 'IPS', 'Bahasa Indonesia', 'PJOK'],
                datasets: [{
                    data: [12, 9, 7, 14, 5],
                    backgroundColor: ['#2ecc71', '#3498db', '#f1c40f', '#e67e22', '#e74c3c'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        const kelasChart = new Chart(document.getElementById('kelasChart'), {
            type: 'bar',
            data: {
                labels: ['Kelas 1', 'Kelas 2', 'Kelas 3', 'Kelas 4', 'Kelas 5', 'Kelas 6'],
                datasets: [{
                    label: 'Jumlah Siswa',
                    data: [30, 28, 26, 32, 29, 31],
                    backgroundColor: '#8e44ad',
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>
@endsection
