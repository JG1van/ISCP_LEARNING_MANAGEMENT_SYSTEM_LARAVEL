@extends('admin.layouts.app')

@section('title', 'Manajemen Produk')
@section('page_title', 'Manajemen Produk ')

@section('content')
    <div class="row g-2 align-items-end mb-3">
        <div class="col-md-8">
            <label class="form-label">Pencarian</label>
            <input id="searchInput" type="text" class="form-control" placeholder="Cari Nama Produk..." />
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.produk.create') }}" class="btn btn-add w-100">
                <i class="fas fa-plus me-2"></i>Tambah Produk
            </a>
        </div>
    </div>

    <div class="table-responsive  table-wrapper">
        <table class="table table-bordered w-100 table-hover text-center align-middle" id="produkTable">
            <thead>
                <tr>
                    <th style="width:50px">No</th>
                    <th>Nama Produk</th>
                    <th>Kelas</th>
                    <th>Semester</th>
                    <th>Daftar Materi</th>
                    <th style="width:150px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->name }}</td>

                        {{--   Kelas --}}
                        <td>
                            @if (empty($item->grade) || $item->grade == 0)
                                <span class="text-muted">Belum Ditentukan</span>
                            @else
                                Kelas {{ $item->grade }}
                            @endif
                        </td>

                        {{--   Semester --}}
                        <td>
                            @if (empty($item->semester) || $item->semester == 0)
                                <span class="text-muted">Belum Ditentukan</span>
                            @elseif ($item->semester == 1)
                                Semester 1 (Ganjil)
                            @elseif ($item->semester == 2)
                                Semester 2 (Genap)
                            @else
                                <span class="text-muted">Tidak Valid</span>
                            @endif
                        </td>

                        {{--   Daftar Materi --}}
                        <td>
                            @if (!empty($item->lesson_names))
                                {{ implode(', ', $item->lesson_names) }}
                            @else
                                <span class="text-muted">Tidak ada materi</span>
                            @endif
                        </td>

                        {{-- ⚙️ Tombol Aksi --}}
                        <td>
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('admin.produk.edit', $item->id) }}" class="btn btn-alt-1">Edit</a>
                                <button type="button" class="btn btn-alt-2"
                                    onclick="hapusProduk('{{ $item->id }}', '{{ $item->name }}')">
                                    Hapus
                                </button>

                                <form id="delete-form-{{ $item->id }}"
                                    action="{{ route('admin.produk.destroy', $item->id) }}" method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-muted text-center">Tidak ada data produk.</td>
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
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Filter manual
            const searchInput = document.getElementById('searchInput');
            const table = document.getElementById('produkTable').getElementsByTagName('tbody')[0];

            searchInput.addEventListener('keyup', function() {
                const filter = searchInput.value.toLowerCase();
                const rows = table.getElementsByTagName('tr');

                for (let i = 0; i < rows.length; i++) {
                    const namaProdukCell = rows[i].getElementsByTagName('td')[1];
                    if (namaProdukCell) {
                        const txtValue = namaProdukCell.textContent || namaProdukCell.innerText;
                        rows[i].style.display = txtValue.toLowerCase().includes(filter) ? '' : 'none';
                    }
                }
            });

            // Hapus produk
            window.hapusProduk = function(id, nama) {
                Swal.fire({
                    title: 'Hapus Produk',
                    text: `Yakin ingin menghapus produk "${nama}"?`,
                    icon: 'warning',
                    confirmButtonColor: '#B05B3B',
                    cancelButtonColor: '#D79771',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById(`delete-form-${id}`).submit();
                    }
                });
            };
        });
    </script>
@endsection
