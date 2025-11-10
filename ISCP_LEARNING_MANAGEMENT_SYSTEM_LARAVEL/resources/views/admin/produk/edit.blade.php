@extends('layouts.app')

@section('title', 'Edit Produk')
@section('page_title', 'Edit Produk Pembelajaran')

@section('content')
    <form action="{{ route('admin.produk.update', $product->id) }}" method="POST" id="formProduk">
        @csrf
        @method('PUT')

        {{-- ID Produk --}}
        <div class="mb-3">
            <label class="form-label">ID Produk</label>
            <input type="text" class="form-control" value="{{ $product->id }}" readonly>
        </div>

        {{-- Nama Produk --}}
        <div class="mb-3">
            <label for="name" class="form-label required">Nama Produk</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ $product->name }}"
                placeholder="Masukkan nama produk" required>
        </div>

        {{-- Tabel Materi --}}
        <label>Daftar Materi</label>
        <div class="table-responsive table-wrapper mb-2">
            <table class="table table-bordered w-100 table-hover text-center align-middle" id="tabelMateri">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Materi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                    <tr>
                        <th colspan="3"></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <button type="button" class="btn btn-add mb-3" data-bs-toggle="modal" data-bs-target="#modalMateri">
            + Tambah Materi
        </button>

        {{-- Kelas --}}
        <div class="mb-3">
            <label for="grade" class="form-label required">Kelas</label>
            <select name="grade" id="grade" class="form-select" required>
                <option value="">== Pilih ==</option>
                @for ($i = 1; $i <= 6; $i++)
                    <option value="{{ $i }}" {{ $product->grade == $i ? 'selected' : '' }}>Kelas
                        {{ $i }}</option>
                @endfor
                <option value="0" {{ $product->grade == 0 ? 'selected' : '' }}>Belum Ditentukan</option>
            </select>
        </div>

        {{-- Hak Akses Kelas --}}
        <div class="mb-3">
            <label class="form-label required">Hak Akses Kelas</label>
            <div class="d-flex flex-wrap gap-2">
                @php
                    $akses = is_array($product->grade_category)
                        ? $product->grade_category
                        : json_decode($product->grade_category ?? '[]', true);
                @endphp
                @for ($i = 1; $i <= 6; $i++)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="grade_category[]" value="{{ $i }}"
                            id="hakAkses{{ $i }}" {{ in_array($i, $akses ?? []) ? 'checked' : '' }}>
                        <label class="form-check-label" for="hakAkses{{ $i }}">Kelas
                            {{ $i }}</label>
                    </div>
                @endfor
            </div>
        </div>

        {{-- Semester --}}
        <div class="mb-3">
            <label for="semester" class="form-label required">Semester</label>
            <select name="semester" id="semester" class="form-select" required>
                <option value="">== Pilih ==</option>
                <option value="1" {{ $product->semester == '1' ? 'selected' : '' }}>Ganjil (1)</option>
                <option value="2" {{ $product->semester == '2' ? 'selected' : '' }}>Genap (2)</option>
                <option value="0" {{ $product->semester == '0' ? 'selected' : '' }}>Belum Ditentukan</option>
            </select>
        </div>

        <input type="hidden" name="materi_json" id="materiJson">

        <button type="button" class="btn btn-add w-100" onclick="konfirmasiSimpan()">
            Simpan Perubahan
        </button>
    </form>

    {{-- Modal Tambah Materi --}}
    <div class="modal fade" id="modalMateri" tabindex="-1" aria-labelledby="modalMateriLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Materi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    {{-- Input Pencarian --}}
                    <div class="mb-3">
                        <input type="text" id="searchLesson" class="form-control" placeholder="Cari pelajaran...">
                    </div>

                    <div class="table-responsive table-wrapper">
                        <table class="table table-bordered w-100 table-hover text-center align-middle" id="tabelLesson">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Pelajaran</th>
                                    <th>Kelas</th>
                                    <th>Semester</th>
                                    <th>Kategori</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($lessons as $index => $lesson)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $lesson->name }}</td>
                                        <td>Kelas {{ $lesson->grade }}</td>
                                        <td>
                                            @if ($lesson->semester == 1)
                                                Semester 1 (Ganjil)
                                            @elseif ($lesson->semester == 2)
                                                Semester 2 (Genap)
                                            @else
                                                Belum Ditentukan
                                            @endif
                                        </td>
                                        <td>
                                            @if ($lesson->category == 1)
                                                Teori Pelajaran
                                            @elseif ($lesson->category == 2)
                                                Soal
                                            @else
                                                <span class="text-muted">Tidak Diketahui</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-add"
                                                onclick="pilihMateri('{{ $lesson->id }}','{{ $lesson->name }}')">
                                                Pilih
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="6"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const materiData = @json(collect($lessons)->whereIn('id', $product->lesson_id ?? [])->map(fn($l) => ['id' => $l->id, 'nama' => $l->name])->values());
            const tabelBody = document.querySelector("#tabelMateri tbody");
            const searchInput = document.getElementById("searchLesson");

            function renderTabelMateri() {
                tabelBody.innerHTML = '';
                if (materiData.length === 0) {
                    tabelBody.innerHTML = `<tr><td colspan="3" class="text-muted">Belum ada materi.</td></tr>`;
                    document.getElementById("materiJson").value = "";
                    return;
                }
                let html = '';
                materiData.forEach((item, index) => {
                    html += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${item.nama}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger" onclick="hapusMateri(${index})">Hapus</button>
                    </td>
                </tr>`;
                });
                tabelBody.innerHTML = html;
                document.getElementById("materiJson").value = JSON.stringify(materiData);
            }

            window.hapusMateri = function(index) {
                Swal.fire({
                    title: 'Hapus Materi?',
                    text: 'Yakin ingin menghapus materi ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#B05B3B',
                    cancelButtonColor: '#D79771',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        materiData.splice(index, 1);
                        renderTabelMateri();
                        Swal.fire({
                            icon: 'success',
                            title: 'Terhapus!',
                            text: 'Materi berhasil dihapus.',
                            timer: 1200,
                            showConfirmButton: false
                        });
                    }
                });
            }

            window.pilihMateri = function(id, nama) {
                if (materiData.find(m => m.id == id)) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Materi sudah ditambahkan!',
                        text: 'Silakan pilih materi lain.',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    return;
                }
                materiData.push({
                    id,
                    nama
                });
                renderTabelMateri();
                bootstrap.Modal.getInstance(document.getElementById("modalMateri")).hide();
                Swal.fire({
                    icon: 'success',
                    title: 'Materi ditambahkan!',
                    showConfirmButton: false,
                    timer: 1000
                });
            }

            window.konfirmasiSimpan = function() {
                const nama = document.getElementById("name").value.trim();
                const grade = document.getElementById("grade").value || "0";
                const hakAkses = document.querySelectorAll('input[name="grade_category[]"]:checked');
                const semester = document.getElementById("semester").value || "0";

                if (!nama) return Swal.fire('Peringatan', 'Nama produk harus diisi.', 'warning');
                if (hakAkses.length === 0) return Swal.fire('Peringatan',
                    'Pilih setidaknya satu hak akses kelas.', 'warning');
                if (materiData.length === 0) return Swal.fire('Peringatan', 'Tambahkan minimal satu materi.',
                    'warning');

                Swal.fire({
                    title: 'Simpan Perubahan?',
                    text: 'Pastikan data yang diubah sudah benar.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#B05B3B',
                    cancelButtonColor: '#D79771',
                    confirmButtonText: 'Ya, Simpan',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById("grade").value = grade;
                        document.getElementById("semester").value = semester;
                        document.getElementById("formProduk").submit();
                    }
                });
            }

            //   Pencarian di modal
            searchInput.addEventListener("keyup", function() {
                const filter = searchInput.value.toLowerCase();
                document.querySelectorAll("#tabelLesson tbody tr").forEach(row => {
                    const nama = row.cells[1].textContent.toLowerCase();
                    row.style.display = nama.includes(filter) ? "" : "none";
                });
            });

            renderTabelMateri();
        });
    </script>
@endsection
