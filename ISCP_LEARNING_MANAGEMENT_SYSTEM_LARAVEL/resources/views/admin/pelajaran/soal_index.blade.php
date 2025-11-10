@extends('layouts.app')

@section('title', 'Daftar Soal')
@section('page_title')
    Daftar Soal {{ $exercise->title }} - {{ $lesson->name }}
@endsection


@section('content')
    <div class="container-fluid">

        {{-- 🔹 Filter Kompetensi + Model Soal + Tombol Tambah --}}
        <div class="row g-2 align-items-end mb-3">
            {{-- Filter Kompetensi (KD) --}}
            <div class="col-md-4">
                <label for="filterKD" class="form-label fw-bold">Filter Kompetensi (KD)</label>
                <select id="filterKD" class="form-select">
                    <option value="">Semua Kompetensi</option>
                    @foreach ($competences as $competence)
                        <option value="{{ $competence->id }}">
                            {{ $competence->point }} - {{ Str::limit($competence->description, 60) }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Model Soal --}}
            <div class="col-md-4">
                <label for="filterModel" class="form-label fw-bold">Filter Model Soal</label>
                <select id="filterModel" class="form-select">
                    <option value="">Semua Model Soal</option>
                    @foreach ($exerciseModels as $model)
                        <option value="{{ $model->id }}">{{ $model->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Tombol Tambah Soal --}}
            <div class="col-md-4 text-end">
                <a href="{{ route('admin.pelajaran.latihan_soal.soal.create', [
                    'lesson_id' => $lesson->id,
                    'exercise_id' => $exercise->id,
                ]) }}"
                    class="btn btn-add w-100">
                    <i class="fas fa-plus me-2"></i> Tambah Soal
                </a>
            </div>
        </div>

        {{-- 🔹 Daftar Soal --}}
        @php
            $modelNames = [
                1 => 'Pilihan Ganda',
                2 => 'Pilihan Ganda Banyak',
                3 => 'Pernyataan',
                4 => 'Isian',
                5 => 'Uraian',
                6 => 'Iya / Tidak',
                7 => 'Argumen',
            ];
        @endphp

        @forelse ($exerciseItems->sortBy('id') as $item)
            <div class="card border-0 shadow-sm mb-4 soal-item" data-kd="{{ $item->competence_id }}"
                data-model="{{ $item->exercise_model_id }}">

                {{-- HEADER --}}
                <div class="card-header d-flex justify-content-between align-items-center bg-light">
                    <div>
                        <h6 class="fw-bold mb-0">
                            Soal Nomor {{ $item->exercise_number }}
                            <small class="text-muted">
                                ({{ $modelNames[$item->exercise_model_id] ?? 'Model Tidak Dikenal' }})
                            </small>
                        </h6>

                        {{-- Kompetensi --}}
                        @if ($item->competence)
                            <span class="badge bg-info text-dark mt-1 text-start">
                                KD: {{ $item->competence->point }}
                            </span>
                        @endif

                        {{-- Pembuat Soal --}}
                        @if ($item->admin)
                            <p class="mb-0 mt-1 text-secondary small" style="font-size:10px">Dibuat oleh:
                                {{ $item->admin->username }}
                                (Admin)
                            </p>
                        @elseif ($item->user)
                            <p class="mb-0 mt-1 text-secondary small" style="font-size:10px">Dibuat oleh:
                                {{ $item->user->username }}
                                (User)
                            </p>
                        @endif
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.pelajaran.latihan_soal.soal.edit', [
                            'lesson_id' => $lesson->id,
                            'exercise_id' => $exercise->id,
                            'item_id' => $item->id,
                        ]) }}"
                            class="btn btn-sm-1">
                            Edit
                        </a>
                        <button class="btn btn-sm-2"
                            onclick="hapusSoal('{{ $item->id }}', '{{ addslashes(Str::limit(strip_tags($item->question), 50)) }}')">
                            Hapus
                        </button>
                    </div>
                </div>

                {{-- BODY --}}
                <div class="card-body">
                    <div class="fw-semibold mb-2">{!! $item->question !!}</div>

                    {{-- Model 1 & 2 (Pilihan Ganda) --}}
                    @if (in_array($item->exercise_model_id, [1, 2]) && $item->selection)
                        @php
                            $opsi = json_decode($item->selection, true);
                        @endphp

                        <div class="option-container d-flex flex-wrap gap-2">
                            @foreach ($opsi as $k => $v)
                                <div class="option-item d-flex" style="width:48%;">
                                    <span class="fw-bold me-2">{{ chr(65 + $k) }}.</span>
                                    <div>{!! $v !!}</div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- FOOTER --}}
                <div class="card-footer bg-white">
                    @if (in_array($item->exercise_model_id, [1, 2]))
                        <b>Kunci Jawaban:</b>
                        <span class="text-danger">
                            {{ is_array(json_decode($item->answer, true)) ? implode(', ', json_decode($item->answer, true)) : $item->answer }}
                        </span>
                    @elseif (in_array($item->exercise_model_id, [3, 6]))
                        <b>Kunci Jawaban:</b>
                        <span class="text-danger">{{ ucfirst($item->answer) }}</span>
                    @elseif ($item->exercise_model_id == 4)
                        <b>Kunci Jawaban:</b>
                        <span class="text-danger">{{ $item->answer }}</span>
                    @elseif (in_array($item->exercise_model_id, [5, 7]))
                        <p class="fw-bold mb-1">Kunci Jawaban:</p>
                        <div class="bg-light p-2 rounded">{!! $item->answer !!}</div>
                    @endif
                </div>
            </div>
        @empty
            <div class="alert alert-light border text-center mt-4">
                Belum ada soal yang dibuat untuk latihan ini.
            </div>
        @endforelse
    </div>
@endsection

@section('js')
    <script>
        // 🔸 Filter model soal
        document.getElementById('filterModel').addEventListener('change', filterSoal);
        document.getElementById('filterKD').addEventListener('change', filterSoal);

        function filterSoal() {
            const selectedModel = document.getElementById('filterModel').value;
            const selectedKD = document.getElementById('filterKD').value;

            document.querySelectorAll('.soal-item').forEach(item => {
                const modelMatch = selectedModel === "" || item.dataset.model === selectedModel;
                const kdMatch = selectedKD === "" || item.dataset.kd === selectedKD;
                item.style.display = (modelMatch && kdMatch) ? "block" : "none";
            });
        }

        // 🔸 Hapus soal
        function hapusSoal(id, judul) {
            Swal.fire({
                title: "Hapus Soal?",
                text: `Yakin ingin menghapus soal: "${judul}"?`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, hapus",
                cancelButtonText: "Batal",
                confirmButtonColor: "#d33",
            }).then(result => {
                if (result.isConfirmed) {
                    fetch(`{{ url('/admin/pelajaran/' . $lesson->id . '/latihan_soal/' . $exercise->id . '/soal') }}/${id}`, {
                            method: "DELETE",
                            headers: {
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire("Berhasil!", data.message, "success");
                                setTimeout(() => location.reload(), 1000);
                            } else {
                                Swal.fire("Gagal!", data.message, "error");
                            }
                        })
                        .catch(() => Swal.fire("Gagal!", "Terjadi kesalahan server.", "error"));
                }
            });
        }
    </script>
@endsection
