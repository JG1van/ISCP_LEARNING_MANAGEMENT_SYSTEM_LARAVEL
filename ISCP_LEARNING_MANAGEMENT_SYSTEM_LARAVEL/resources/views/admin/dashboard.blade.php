@extends('admin.layouts.app')

@section('title', 'Dashboard LMS')
@section('page_title', 'Dashboard Learning Management System')

@section('content')
    <div class="container my-4">

        {{-- Kartu sambutan --}}
        <div class="row mb-3 align-items-stretch g-4">
            <div class="col-md-8">
                <div
                    class="card p-4 d-flex flex-column flex-md-row justify-content-between align-items-center border text-center size-card">
                    <div class="welcome-illustration px-2">
                        <img src="{{ asset('images/ilustrasi-selamat-datang-1.png') }}" alt="Ilustrasi Kiri">
                    </div>
                    <div class="flex-grow-1 px-3">
                        <h5 class="fw-bold mb-2">Halo, <span
                                class="text-primary">{{ Auth::user()->username ?? 'Admin' }}</span>
                            👋</h5>
                        <p class="fs-6 text-muted mb-0">Selamat datang di</p>
                        <p class="fs-6 fw-semibold text-dark mb-0">Sistem Manajemen Pembelajaran (LMS)</p>
                    </div>
                    <div class="welcome-illustration px-2">
                        <img src="{{ asset('images/ilustrasi-selamat-datang-2.png') }}" alt="Ilustrasi Kanan">
                    </div>
                </div>
            </div>

            {{-- Statistik kecil --}}
            <div class="col-md-4">
                <div class="row g-4">
                    <div class="col-6">
                        <div class="card p-3 text-center border" style="min-height: 140px;">
                            <i class="bi bi-book text-primary" style="font-size: 2rem;"></i>
                            <h6 class="mt-2 text-truncate">Total Materi</h6>
                            <h4 class="fw-bold text-dark fs-5">{{ $totalMateri }}</h4>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card p-3 text-center border" style="min-height: 140px;">
                            <i class="bi bi-people text-success" style="font-size: 2rem;"></i>
                            <h6 class="mt-2 text-truncate">Total Siswa</h6>
                            <h4 class="fw-bold text-dark fs-5">{{ $totalSiswa }}</h4>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card p-3 text-center border" style="min-height: 140px;">
                            <i class="bi bi-person-workspace text-info" style="font-size: 2rem;"></i>
                            <h6 class="mt-2 text-truncate">Total Guru</h6>
                            <h4 class="fw-bold text-dark fs-5">{{ $totalGuru }}</h4>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card p-3 text-center border" style="min-height: 140px;">
                            <i class="bi bi-journal-bookmark text-warning" style="font-size: 2rem;"></i>
                            <h6 class="mt-2 text-truncate">Total Kelas</h6>
                            <h4 class="fw-bold text-dark fs-5">{{ $totalKelas }}</h4>
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
                        <div class="fw-semibold">Total Produk Pembelajaran</div>
                        <div class="fw-bold text-dark fs-6">{{ $totalProduk }} Modul</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3 border d-flex flex-row align-items-center" style="gap: 15px;">
                    <i class="bi bi-collection text-primary" style="font-size: 2rem;"></i>
                    <div>
                        <div class="fw-semibold">Total Mata Pelajaran</div>
                        <div class="fw-bold text-dark fs-6">{{ $totalMapel }} Mapel</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3 border d-flex flex-row align-items-center" style="gap: 15px;">
                    <i class="bi bi-key text-success" style="font-size: 2rem;"></i>
                    <div>
                        <div class="fw-semibold">Total Serial</div>
                        <div class="fw-bold text-dark fs-6">{{ $totalSerial }} Kode</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Grafik Donat --}}
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="card p-3 border">
                    <h6 class="text-center"><i class="bi bi-pie-chart"></i> Distribusi Pelajaran per Mata Pelajaran</h6>
                    <div style="height: 300px; display: flex; justify-content: center; align-items: center;">
                        <canvas id="materiChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card p-3 border">
                    <h6 class="text-center"><i class="bi bi-bar-chart"></i> Jumlah Siswa per Kelas</h6>
                    <div style="height: 300px; display: flex; justify-content: center; align-items: center;">
                        <canvas id="kelasChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Data dari controller
        const materiLabels = {!! json_encode($materiPerMapel->pluck('mapel')) !!};
        const materiData = {!! json_encode($materiPerMapel->pluck('total')) !!};
        const kelasLabels = {!! json_encode($siswaPerKelas->pluck('kelas')) !!};
        const kelasData = {!! json_encode($siswaPerKelas->pluck('total')) !!};

        // Fungsi warna acak dengan saturasi tinggi
        function generateColors(count) {
            const colors = [];
            for (let i = 0; i < count; i++) {
                const hue = Math.floor(360 * Math.random());
                const saturation = 70 + Math.random() * 30; // 70%–100%
                const lightness = 45 + Math.random() * 15; // 45%–60%
                colors.push(`hsl(${hue}, ${saturation}%, ${lightness}%)`);
            }
            return colors;
        }

        const materiColors = generateColors(materiData.length);
        const kelasColors = generateColors(kelasData.length);

        // Chart: Distribusi Materi per Mapel
        new Chart(document.getElementById("materiChart").getContext("2d"), {
            type: "doughnut",
            data: {
                labels: materiLabels,
                datasets: [{
                    data: materiData,
                    backgroundColor: materiColors,
                    borderColor: "#ffffff",
                    borderWidth: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: 10
                },
                plugins: {
                    legend: {
                        position: "bottom",
                        labels: {
                            color: "#333",
                            font: {
                                size: 13,
                                weight: "bold"
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.label}: ${context.parsed}`;
                            }
                        }
                    }
                }
            }
        });

        // Chart: Jumlah Siswa per Kelas
        new Chart(document.getElementById("kelasChart").getContext("2d"), {
            type: "doughnut",
            data: {
                labels: kelasLabels,
                datasets: [{
                    data: kelasData,
                    backgroundColor: kelasColors,
                    borderColor: "#ffffff",
                    borderWidth: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: 10
                },
                plugins: {
                    legend: {
                        position: "bottom",
                        labels: {
                            color: "#333",
                            font: {
                                size: 13,
                                weight: "bold"
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.label}: ${context.parsed}`;
                            }
                        }
                    }
                }
            }
        });
    </script>


@endsection
