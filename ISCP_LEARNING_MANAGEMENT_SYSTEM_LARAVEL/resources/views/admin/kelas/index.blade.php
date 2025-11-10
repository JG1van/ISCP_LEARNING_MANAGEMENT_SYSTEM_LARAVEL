@extends('layouts.app')

@section('title', 'Manajemen Kelas')
@section('page_title', 'Manajemen Kelas')

@section('content')
    {{-- ⚠️ PERINGATAN SERIAL --}}
    @if (!empty($warningSerials))
        <div class="alert alert-danger border-0 shadow-sm">
            <h5 class="fw-bold text-white bg-danger p-2 rounded-top">
                ⚠️ PERINGATAN SISTEM: SERIAL MELEBIHI BATAS KELAS YANG DIIZINKAN
            </h5>
            <div class="bg-light text-dark p-3 rounded-bottom">
                <p class="mb-2">
                    Ditemukan beberapa <strong>Serial</strong> yang memiliki jumlah kelas
                    <strong>melebihi batas paket</strong>. Mohon segera periksa dan sesuaikan.
                </p>
                <ul class="mb-0">
                    @foreach ($warningSerials as $warn)
                        <li class="mb-3 p-2 border-start border-3 border-danger bg-light rounded">
                            <strong>Kode Serial:</strong> <span class="text-danger">{{ $warn['kode_serial'] }}</span><br>
                            <strong>Nama Pengguna:</strong> {{ $warn['username'] }}<br>
                            <strong>Batas Paket:</strong> {{ $warn['paket'] }} kelas<br>
                            <strong>Jumlah Saat Ini:</strong> {{ $warn['kelas'] }} kelas<br>
                            <strong>Daftar Kelas:</strong>
                            <span class="text-muted">{{ implode(', ', $warn['daftar_kelas']) }}</span>
                        </li>
                    @endforeach
                </ul>
                <hr>
                <small class="text-danger fw-semibold">
                    * Disarankan untuk menghapus atau memindahkan kelas yang berlebih agar sesuai dengan paket serial.
                </small>
            </div>
        </div>
    @endif

    {{--   Pencarian & Tambah --}}
    <div class="row g-2 align-items-end mb-3">
        <div class="col-md-8">
            <label class="form-label">Pencarian</label>
            <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" id="searchInput"
                type="text" class="form-control" placeholder="Cari Nama Kelas / Guru..." autocomplete="off">
        </div>
        <div class="col-md-4 text-end">
            <button class="btn btn-add w-100" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="fas fa-plus me-2"></i>Tambah Kelas
            </button>
        </div>
    </div>

    {{-- 📋 TABEL DATA --}}
    <div class="table-responsive table-wrapper">
        <table class="table table-bordered w-100 table-hover text-center align-middle">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Kelas</th>
                    <th>Nama Guru</th>
                    <th>Kelas</th>
                    <th>Jumlah Siswa</th>
                    <th style="width:200px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $i => $c)
                    <tr id="row{{ $c->id }}">
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $c->name }}</td>
                        <td>{{ $c->serial && $c->serial->user ? $c->serial->user->name : 'Belum Ditentukan' }}</td>
                        <td>{{ $c->grade }}</td>
                        <td>{{ $c->students_count ?? 0 }}</td>
                        <td>
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ url('admin/siswa/create?classroom_id=' . $c->id) }}"
                                    class="btn btn-add">Siswa</a>
                                <button class="btn btn-sm-1" onclick="editKelas({{ $c->id }})">Edit</button>
                                <button class="btn btn-sm-2"
                                    onclick="hapusKelas({{ $c->id }}, '{{ $c->name }}')">Hapus</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-3">Belum ada data kelas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- 🧩 MODAL TAMBAH KELAS --}}
    <div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="formTambah" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Kelas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row g-3">
                    <div class="col-12">
                        <label class="form-label">Nama Kelas</label>
                        <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" type="text"
                            name="name" class="form-control" required autocomplete="off">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Kelas</label>
                        <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" type="text"
                            name="grade" class="form-control" required autocomplete="off">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Serial / Guru</label>
                        <div class="input-group">
                            <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                type="hidden" name="serial_id" id="tambahSerialId">
                            <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                type="text" id="tambahSerialText" class="form-control" placeholder="Belum dipilih"
                                readonly>
                            <button type="button" class="btn btn-secondary" onclick="openPilihSerial('tambah')">
                                Pilih Serial
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-add w-100">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- 🧩 MODAL EDIT KELAS --}}
    <div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="formEdit" class="modal-content">
                @csrf
                @method('PUT')
                <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" type="hidden"
                    id="editId" name="id">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Kelas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row g-3">
                    <div class="col-12">
                        <label class="form-label">Nama Kelas</label>
                        <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                            type="text" id="editName" name="name" class="form-control" required
                            autocomplete="off">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Kelas</label>
                        <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                            type="text" id="editGrade" name="grade" class="form-control" required
                            autocomplete="off">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Serial / Guru</label>
                        <div class="input-group">
                            <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                type="hidden" name="serial_id" id="editSerialId">
                            <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                type="text" id="editSerialText" class="form-control" placeholder="Belum dipilih"
                                readonly>
                            <button type="button" class="btn btn-secondary" onclick="openPilihSerial('edit')">
                                Pilih Serial
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-add w-100">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    {{--   POPUP PILIH SERIAL --}}
    <div class="modal fade" id="modalPilihSerial" tabindex="-1" aria-hidden="true" data-bs-backdrop="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pilih Serial / Guru</h5>
                    <button type="button" class="btn-close btn-close-black" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" type="text"
                        id="searchSerial" class="form-control mb-3" placeholder="Cari serial atau guru...">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-center align-middle">
                            <thead>
                                <tr>
                                    <th>Kode Serial</th>
                                    <th>Nama Guru</th>
                                    <th>Paket</th>
                                    <th>Pilih</th>
                                </tr>
                            </thead>
                            <tbody id="serialTableBody">
                                @foreach ($serials as $s)
                                    <tr>
                                        <td>{{ $s->serial }}</td>
                                        <td>{{ $s->user->name ?? 'Belum Ditentukan' }}</td>
                                        <td>{{ $s->paket ?? '-' }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-add"
                                                onclick="pilihSerial('{{ $s->id }}', '{{ $s->serial }}', '{{ $s->user->name ?? 'Belum Ditentukan' }}')">
                                                Pilih
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        let currentTarget = null; // 'tambah' atau 'edit'

        // ========== POP SERIAL ==========
        function openPilihSerial(target) {
            currentTarget = target;
            const modal = new bootstrap.Modal(document.getElementById('modalPilihSerial'));
            modal.show();
        }

        function pilihSerial(id, serial, guru) {
            if (currentTarget === 'tambah') {
                document.getElementById('tambahSerialId').value = id;
                document.getElementById('tambahSerialText').value = serial + ' - ' + guru;
            } else {
                document.getElementById('editSerialId').value = id;
                document.getElementById('editSerialText').value = serial + ' - ' + guru;
            }
            // Tutup popup kedua saja
            bootstrap.Modal.getInstance(document.getElementById('modalPilihSerial')).hide();
        }

        //   filter serial
        document.getElementById('searchSerial').addEventListener('keyup', function() {
            const keyword = this.value.toLowerCase();
            document.querySelectorAll('#serialTableBody tr').forEach(tr => {
                const text = tr.innerText.toLowerCase();
                tr.style.display = text.includes(keyword) ? '' : 'none';
            });
        });

        // ========== SWEETALERT NOTIF ==========
        const notifSuccess = (msg) => Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: msg,
            timer: 1800,
            showConfirmButton: false
        });
        const notifError = (msg) => Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: msg
        });

        // ========== PENCARIAN TABEL KELAS ==========
        document.getElementById("searchInput").addEventListener("keyup", function() {
            const keyword = this.value.toLowerCase();
            document.querySelectorAll("tbody tr").forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(keyword) ? "" : "none";
            });
        });

        // ========== TAMBAH / EDIT / HAPUS ==========
        document.getElementById("formTambah").addEventListener("submit", async function(e) {
            e.preventDefault();
            const btn = this.querySelector("button[type='submit']");
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
            try {
                const res = await fetch("{{ route('admin.kelas.store') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: new FormData(this)
                });
                const result = await res.json();
                if (result.success) {
                    notifSuccess(result.message);
                    setTimeout(() => location.reload(), 800);
                } else notifError(result.message);
            } catch (err) {
                notifError(err.message);
            }
            btn.disabled = false;
            btn.innerHTML = 'Simpan';
        });

        async function editKelas(id) {
            const res = await fetch(`/admin/kelas/${id}/edit`);
            const result = await res.json();
            if (result.success) {
                const c = result.data;
                document.getElementById("editId").value = c.id;
                document.getElementById("editName").value = c.name;
                document.getElementById("editGrade").value = c.grade;
                if (c.serial) {
                    document.getElementById("editSerialId").value = c.serial.id;
                    document.getElementById("editSerialText").value = c.serial.serial + ' - ' + (c.serial.user?.name ??
                        'Belum Ditentukan');
                }
                new bootstrap.Modal(document.getElementById("modalEdit")).show();
            } else notifError(result.message);
        }

        document.getElementById("formEdit").addEventListener("submit", async function(e) {
            e.preventDefault();
            const id = document.getElementById("editId").value;
            const btn = this.querySelector("button[type='submit']");
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
            try {
                const res = await fetch(`/admin/kelas/${id}`, {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "X-HTTP-Method-Override": "PUT"
                    },
                    body: new FormData(this)
                });
                const result = await res.json();
                if (result.success) {
                    notifSuccess(result.message);
                    setTimeout(() => location.reload(), 800);
                } else notifError(result.message);
            } catch (err) {
                notifError(err.message);
            }
            btn.disabled = false;
            btn.innerHTML = 'Simpan Perubahan';
        });

        function hapusKelas(id, name) {
            Swal.fire({
                title: 'Hapus Kelas?',
                text: `Yakin ingin menghapus "${name}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                confirmButtonColor: '#B05B3B',
                cancelButtonColor: '#D79771',
                reverseButtons: true
            }).then(async result => {
                if (result.isConfirmed) {
                    const res = await fetch(`/admin/kelas/${id}`, {
                        method: "DELETE",
                        headers: {
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        }
                    });
                    const out = await res.json();
                    if (out.success) {
                        notifSuccess(out.message);
                        document.getElementById(`row${id}`).remove();
                    } else notifError(out.message);
                }
            });
        }
    </script>
@endsection
