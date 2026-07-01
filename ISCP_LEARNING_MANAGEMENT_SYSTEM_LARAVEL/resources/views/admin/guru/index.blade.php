@extends('admin.layouts.app')

@section('title', 'Manajemen Guru')
@section('page_title', 'Manajemen Guru')

@section('content')
    <div class="row g-2 align-items-end mb-3">
        <div class="col-md-8">
            <label class="form-label">Pencarian</label>
            <input id="searchInput" type="text" class="form-control" autocomplete="off" autocorrect="off" autocapitalize="off"
                spellcheck="false" placeholder="Cari Nama Guru...">
        </div>

        <div class="col-md-4 text-end">
            <button class="btn btn-add w-100" id="btnTambah" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="fas fa-plus me-2"></i>Tambah Guru
            </button>
        </div>
    </div>

    {{-- Tabel Guru --}}
    <div class="table-responsive table-wrapper">
        <table class="table table-bordered w-100 table-hover text-center align-middle" id="teacherTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Status</th>
                    <th style="width:170px;">Aksi</th>
                </tr>
            </thead>
            <tbody id="teacherBody">
                @forelse ($teachers as $index => $teacher)
                    <tr id="row{{ $teacher->id }}">
                        <td>{{ $index + 1 }}</td>
                        <td class="teacher-name">{{ $teacher->name }}</td>
                        <td>{{ $teacher->username }}</td>
                        <td>
                            @switch($teacher->role)
                                @case(1)
                                    <span class="badge bg-success">Aktif</span>
                                @break

                                @case(0)
                                    <span class="badge bg-secondary">Tidak Aktif</span>
                                @break

                                @default
                                    <span class="badge bg-danger">Unknown</span>
                            @endswitch
                        </td>
                        <td>
                            <div class="d-flex justify-content-center gap-1">
                                <button class="btn btn-alt-1" onclick="editTeacher('{{ $teacher->id }}')">Detail /
                                    Edit</button>
                                <button class="btn btn-alt-2"
                                    onclick="hapusTeacher('{{ $teacher->id }}', '{{ $teacher->name }}')">Hapus</button>
                            </div>
                        </td>
                    </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-muted text-center">Belum ada data guru.</td>
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

        {{-- Modal Tambah --}}
        <div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form id="formTambah" class="modal-content">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Guru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Nama</label>
                            <input type="text" name="name" class="form-control" required autocomplete="off"
                                autocorrect="off" autocapitalize="off" spellcheck="false">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" required autocomplete="off"
                                autocorrect="off" autocapitalize="off" spellcheck="false">
                        </div>

                        {{-- Password diisi otomatis oleh backend --}}
                        <input type="hidden" name="role" value="1">
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-add w-100">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Edit / Detail --}}
        <div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <form id="formEdit" class="modal-content border-0 shadow-lg rounded-4 overflow-hidden"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="modal-header bg-light border-bottom-0">
                        <h5 class="modal-title fw-bold">Detail / Edit Guru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body p-4">
                        <div class="row align-items-center g-4">
                            {{-- Kartu Guru --}}
                            <div class="col-md-4 d-flex justify-content-center">
                                <div class="bg-light rounded-4 shadow-sm p-4 d-flex flex-column align-items-center justify-content-center"
                                    style="height: 100%; min-height: 250px; position: relative;">
                                    <div class="position-relative">
                                        <img id="editImgPreview" src="{{ asset('images/logo.webp') }}" alt="Foto Guru"
                                            class="rounded-circle border shadow-sm bg-white" width="120" height="120"
                                            style="object-fit: cover;">

                                        <button type="button"
                                            class="btn btn-sm btn-add rounded-circle position-absolute bottom-0 end-0 translate-middle shadow"
                                            id="btnEditPhoto"
                                            style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-pen"></i>
                                        </button>
                                    </div>

                                    <input type="file" id="editImgInput" name="photo" accept="image/*" hidden>

                                    <h6 class="fw-bold mb-0 mt-3" id="editNameCard">Nama Guru</h6>
                                </div>
                            </div>

                            {{-- Detail Guru --}}
                            <div class="col-md-8">
                                <div class="row g-3">
                                    <input type="hidden" id="editId" name="id">

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Nama</label>
                                        <input type="text" id="editName" name="name"
                                            class="form-control border-2 rounded-3" required autocomplete="off"
                                            autocorrect="off" autocapitalize="off" spellcheck="false">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Username</label>
                                        <input type="text" id="editUsername" name="username"
                                            class="form-control border-2 rounded-3" required autocomplete="off"
                                            autocorrect="off" autocapitalize="off" spellcheck="false">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Email</label>
                                        <input type="email" id="editEmail" name="email"
                                            class="form-control border-2 rounded-3" autocomplete="new-email"
                                            inputmode="email" spellcheck="false">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">No. Telepon</label>
                                        <input type="tel" id="editPhone" name="phone"
                                            class="form-control border-2 rounded-3" autocomplete="off" inputmode="tel"
                                            spellcheck="false">
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Alamat</label>
                                        <textarea id="editAddress" name="address" class="form-control border-2 rounded-3" rows="2"></textarea>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Password</label>

                                        <input type="text" id="editPassword" class="form-control border-2 rounded-3 mb-2"
                                            value="********" readonly>

                                        <button type="button" class="btn btn-outline-danger w-100 rounded-3"
                                            id="btnResetPassword">
                                            <i class="fas fa-undo me-1"></i> Reset Password
                                        </button>
                                    </div>


                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Status / Role</label>
                                        <select id="editRole" name="role" class="form-select border-2 rounded-3"
                                            required>
                                            <option value="1">Aktif</option>
                                            <option value="0">Tidak Aktif</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light border-0">
                        <button type="submit" class="btn btn-add w-100 rounded-3 py-2 fw-semibold">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    @endsection

    @section('js')
        <script>
            function notifSuccess(msg) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: msg,
                    timer: 1800,
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


            document.addEventListener('DOMContentLoaded', () => {

                // Pencarian cepat
                const searchInput = document.getElementById('searchInput');
                searchInput.addEventListener('keyup', function() {
                    const keyword = this.value.toLowerCase();
                    document.querySelectorAll('#teacherBody tr').forEach(row => {
                        const namaEl = row.querySelector('.teacher-name');
                        const nama = namaEl ? namaEl.textContent.toLowerCase() : '';
                        row.style.display = nama.includes(keyword) ? '' : 'none';
                    });
                });

                // Reset form tambah saat modal terbuka
                const modalTambah = document.getElementById('modalTambah');
                modalTambah.addEventListener('show.bs.modal', () => {
                    document.getElementById('formTambah').reset();
                });

                // Tombol edit foto (buka file picker)
                document.getElementById('btnEditPhoto').addEventListener('click', () => {
                    document.getElementById('editImgInput').click();
                });

                // Preview gambar saat dipilih
                document.getElementById('editImgInput').addEventListener('change', function(event) {
                    const file = event.target.files[0];
                    if (!file) return;
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        document.getElementById('editImgPreview').src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                });


                document.getElementById('formTambah').addEventListener('submit', async function(e) {
                    e.preventDefault();
                    const form = this;
                    const btn = form.querySelector("button[type='submit']");
                    const formData = new FormData(form);

                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';

                    try {
                        const res = await fetch("{{ route('admin.guru.store') }}", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': "{{ csrf_token() }}"
                            },
                            body: formData
                        });
                        const result = await res.json();
                        if (result.success) {
                            bootstrap.Modal.getInstance(modalTambah).hide();
                            form.reset();
                            notifSuccess(result.message);
                            setTimeout(() => location.reload(), 900);
                        } else {
                            notifError(result.message || 'Gagal menyimpan data.');
                        }
                    } catch (err) {
                        notifError(err.message || 'Terjadi kesalahan jaringan.');
                    } finally {
                        btn.disabled = false;
                        btn.innerHTML = 'Simpan';
                    }
                });


                document.getElementById('formEdit').addEventListener('submit', async function(e) {
                    e.preventDefault();
                    const form = this;
                    const id = document.getElementById('editId').value;
                    const btn = form.querySelector("button[type='submit']");
                    const formData = new FormData(form);

                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';

                    try {
                        const res = await fetch(`/admin/guru/${id}`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                                'X-HTTP-Method-Override': 'PUT'
                            },
                            body: formData
                        });
                        const result = await res.json();
                        if (result.success) {
                            bootstrap.Modal.getInstance(document.getElementById('modalEdit')).hide();
                            notifSuccess(result.message);
                            setTimeout(() => location.reload(), 900);
                        } else {
                            notifError(result.message || 'Gagal memperbarui data.');
                        }
                    } catch (err) {
                        notifError(err.message || 'Terjadi kesalahan jaringan.');
                    } finally {
                        btn.disabled = false;
                        btn.innerHTML = 'Simpan Perubahan';
                    }
                });

                document.getElementById('btnResetPassword').addEventListener('click', function() {
                    const id = document.getElementById('editId').value;
                    if (!id) return notifError('Pilih guru terlebih dahulu.');

                    Swal.fire({
                        title: 'Reset Password?',
                        text: 'Password guru akan dikembalikan ke default (Guru1234).',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Reset',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#B05B3B',
                        cancelButtonColor: '#D79771',
                        reverseButtons: true
                    }).then(result => {
                        if (!result.isConfirmed) return;
                        fetch(`/admin/guru/${id}/reset-password`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                                }
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) notifSuccess(data.message);
                                else notifError(data.message || 'Gagal mereset password.');
                            })
                            .catch(() => notifError('Terjadi kesalahan saat mereset password.'));
                    });
                });

            }); // end DOMContentLoaded


            function editTeacher(id) {
                fetch(`/admin/guru/${id}/edit`)
                    .then(res => res.json())
                    .then(result => {
                        if (!result.success) {
                            return notifError('Data guru tidak ditemukan.');
                        }

                        const t = result.data;

                        // Set field form
                        document.getElementById('editId').value = t.id ?? '';
                        document.getElementById('editName').value = t.name ?? '';
                        document.getElementById('editUsername').value = t.username ?? '';
                        document.getElementById('editEmail').value = t.email ?? '';
                        document.getElementById('editPhone').value = t.phone ?? '';
                        document.getElementById('editAddress').value = t.address ?? '';
                        document.getElementById('editRole').value = (t.role !== undefined ? t.role : 1);
                        document.getElementById('editPassword').value = '********';
                        document.getElementById('editNameCard').innerText = t.name ?? 'Tanpa Nama';

                        // Ganti gambar profil (fallback jika error)
                        const imgPreview = document.getElementById('editImgPreview');
                        imgPreview.onerror = () => imgPreview.src = '{{ asset('images/logo.webp') }}';

                        // Pilih path sesuai penyimpanan public storage: /storage/users/{filename}
                        if (t.img && t.img !== '') {
                            imgPreview.src = `/storage/users/${t.img}`;
                        } else {
                            imgPreview.src = '{{ asset('images/logo.webp') }}';
                        }

                        // Tampilkan modal edit
                        new bootstrap.Modal(document.getElementById('modalEdit')).show();
                    })
                    .catch(() => notifError('Gagal memuat data guru.'));
            }


            function hapusTeacher(id, nama) {
                Swal.fire({
                    title: 'Hapus Guru?',
                    text: `Yakin ingin menghapus "${nama}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#B05B3B',
                    cancelButtonColor: '#D79771',
                    reverseButtons: true
                }).then(result => {
                    if (!result.isConfirmed) return;
                    fetch(`/admin/guru/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': "{{ csrf_token() }}"
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                const row = document.getElementById(`row${id}`);
                                if (row) row.remove();
                                notifSuccess(data.message);
                            } else {
                                notifError(data.message || 'Gagal menghapus data.');
                            }
                        })
                        .catch(() => notifError('Terjadi kesalahan saat menghapus.'));
                });
            }
        </script>
    @endsection
