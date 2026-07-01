@extends('admin.layouts.app')

@section('title', 'Manajemen Serial')
@section('page_title', 'Manajemen Serial')

@section('content')
    {{--   Pencarian & Tombol Tambah --}}
    <div class="row g-2 align-items-end mb-3">

        <div class="col-md-6">
            <label class="form-label">Pencarian</label>
            <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" id="searchInput" type="text"
                class="form-control" placeholder="Cari Serial / Produk / Pengguna..." />
        </div>
        <div class="col-md-2 ">
            <a href="{{ route('admin.serial.riwayat') }}" class="btn btn-alt-1 w-100">
                <i class="fas fa-history me-2"></i>Riwayat
            </a>
        </div>
        <div class="col-md-4 ">
            <button class="btn btn-add w-100" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="fas fa-plus me-2"></i>Tambah Serial
            </button>
        </div>

    </div>

    {{--   Tabel Serial --}}
    <div class="table-responsive table-wrapper">
        <table class="table table-bordered w-100 table-hover text-center align-middle">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Serial</th>
                    <th>Produk</th>
                    <th>Paket Kelas</th>
                    <th>Aktif (bulan)</th>
                    <th>Expired</th>
                    <th>Pengguna</th>
                    <th style="width:320px;">Aksi</th>
                </tr>
            </thead>
            <tbody id="serialBody">
                @forelse ($data as $i => $s)
                    <tr id="row{{ $s->id }}">
                        <td>{{ $i + 1 }}</td>
                        <td class="fw-bold serial-cell" style="cursor:pointer; font-size: 10px;"
                            title="Klik untuk menyalin">
                            {{ $s->serial }}
                        </td>
                        <td>
                            @if ($s->product)
                                {{ $s->product->name }}
                            @else
                                <span class="text-muted">Belum Ditentukan</span>
                            @endif
                        </td>
                        <td>{{ $s->paket }}</td>
                        <td>{{ $s->active }}</td>
                        <td>
                            @if ($s->expired_at)
                                {{ \Carbon\Carbon::parse($s->expired_at)->format('d/m/Y') }}
                            @else
                                <span class="text-muted">Belum Aktif</span>
                            @endif
                        </td>
                        <td>
                            @if ($s->user)
                                {{ $s->user->name }}
                            @else
                                <span class="text-muted">Belum Ditentukan</span>
                            @endif
                        </td>

                        <td>
                            <div class="d-flex justify-content-center gap-1">
                                <button class="btn btn-alt-1" onclick="editSerial({{ $s->id }})">Edit</button>
                                <button class="btn btn-alt-2"
                                    onclick="hapusSerial({{ $s->id }}, '{{ $s->serial }}')">Hapus</button>
                                @if ($s->expired_at)
                                    <button class="btn btn-add"
                                        onclick="perpanjangSerial({{ $s->id }})">Perpanjang</button>
                                @else
                                    <button class="btn btn-add" disabled>Belum Aktif</button>
                                @endif
                                <button class="btn btn-add " onclick="openEmailModal({{ $s->id }})">
                                    Kirim Email
                                </button>

                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-muted text-center">Tidak ada data serial.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="8"></th>
                </tr>
            </tfoot>
        </table>
    </div>

    {{--   Modal Tambah Serial --}}
    <div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="formTambah" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Serial</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row g-3">
                    <div class="col-12">
                        <label class="form-label">Produk</label>
                        <select name="product_id" class="form-select" required>
                            <option value="">== Pilih ==</option>
                            @foreach ($products as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Paket Kelas</label>
                        <select name="paket" class="form-select" required>
                            <option value="">== Pilih Paket ==</option>
                            @for ($i = 1; $i <= 9; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Aktif (bulan)</label>
                        <select name="active" class="form-select" required>
                            <option value="">== Pilih ==</option>
                            @for ($i = 6; $i <= 120; $i += 6)
                                <option value="{{ $i }}">{{ $i }} bulan</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-12">
                        <div class="input-group">
                            <input type="text" id="tambahUserName" class="form-control" placeholder="Belum dipilih"
                                readonly>
                            <input type="hidden" id="tambahUserId" name="user_id">
                            <button type="button" class="btn btn-alt-2" onclick="openUserPopup('tambah')">
                                Pilih
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

    {{--   Modal Edit Serial --}}
    <div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="formEdit" class="modal-content">
                @csrf
                @method('PUT')
                <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" type="hidden"
                    id="editId" name="id">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Serial</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row g-3">
                    <div class="col-12">
                        <label class="form-label">Produk</label>
                        <select id="editProductId" name="product_id" class="form-select" required>
                            @foreach ($products as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Paket Kelas</label>
                        <select id="editPaket" name="paket" class="form-select" required>
                            @for ($i = 1; $i <= 9; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-12">
                        <div class="input-group">
                            <input type="text" id="editUserName" class="form-control" placeholder="Belum dipilih"
                                readonly>
                            <input type="hidden" id="editUserId" name="user_id">
                            <button type="button" class="btn btn-alt-2" onclick="openUserPopup('edit')">
                                Pilih
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
    {{--   Modal Perpanjang Serial --}}
    <div class="modal fade" id="modalExtend" tabindex="-1" aria-labelledby="modalExtendLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form id="formExtend" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="modalExtendLabel">Perpanjang Masa Aktif Serial</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="extendId" name="id">
                    <div class="mb-3">
                        <label class="form-label">Serial</label>
                        <input type="text" id="extendSerial" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Expired Saat Ini</label>
                        <input type="text" id="extendExpired" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tambah (bulan)</label>
                        <select id="extendMonths" name="extend_months" class="form-select" required>
                            <option value="">== Pilih ==</option>
                            @for ($i = 6; $i <= 120; $i += 6)
                                <option value="{{ $i }}">{{ $i }} bulan</option>
                            @endfor
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Perkiraan Tanggal Baru</label>
                        <input type="text" id="extendNewExpired" class="form-control" readonly>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-add w-100">Simpan Perpanjangan</button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="emailModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Kirim Serial ke Email</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    <input type="hidden" id="email_serial_id">

                    <label>Email Pelanggan</label>
                    <input type="email" id="email_to" class="form-control">

                </div>
                <div class="modal-footer">
                    <button class="btn btn-alt-1" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-add" onclick="kirimEmail()">Kirim</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Pilih User -->
    <div class="modal fade" id="modalPilihUser" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pilih Pengguna</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" type="text"
                        id="searchUserInput" class="form-control mb-3" placeholder="Cari nama user...">
                    <div class="table-responsive table-wrapper">
                        <table class="table table-bordered w-100 table-hover text-center align-middle" id="studentTable">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody id="userTablePopup">
                                @forelse ($users->where('role', 1) as $u)
                                    <tr class="user-item" data-id="{{ $u->id }}" data-name="{{ $u->name }}"
                                        data-username="{{ $u->username }}" data-email="{{ $u->email }}"
                                        style="cursor:pointer;">

                                        <td>{{ $u->name ?? '-' }}</td>
                                        <td>{{ $u->username ?? '-' }}</td>
                                        <td>{{ $u->email ?? '-' }}</td>

                                        <td>
                                            <button class="btn btn-add"
                                                onclick="pilihUser('{{ $u->id }}', '{{ $u->name }}')">
                                                Pilih
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-muted text-center">
                                            Belum ada data pengguna / guru.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                            <tfoot>
                                <tr>
                                    <th colspan="4"></th>
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
        //   Notifikasi
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
            text: msg,
            confirmButtonText: 'Tutup'
        });

        //   Pencarian langsung
        document.getElementById("searchInput").addEventListener("keyup", function() {
            const keyword = this.value.toLowerCase();
            document.querySelectorAll("#serialBody tr").forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(keyword) ? "" : "none";
            });
        });

        //   Tambah Serial
        document.getElementById("formTambah").addEventListener("submit", async function(e) {
            e.preventDefault();
            const btn = this.querySelector("button[type='submit']");
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
            try {
                const res = await fetch("{{ route('admin.serial.store') }}", {
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

        //   Edit Serial
        async function editSerial(id) {
            const res = await fetch(`/admin/serial/${id}/edit`);
            const result = await res.json();
            if (result.success) {
                const s = result.data;
                document.getElementById("editId").value = s.id;
                document.getElementById("editProductId").value = s.product_id;
                document.getElementById("editPaket").value = s.paket;
                document.getElementById("editUserId").value = s.user_id ?? "";
                document.getElementById("editUserName").value = s.user ? s.user.name : "Belum dipilih";
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
                const res = await fetch(`/admin/serial/${id}`, {
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

        //   Hapus Serial
        function hapusSerial(id, serial) {
            Swal.fire({
                title: 'Hapus Serial?',
                text: `Yakin ingin menghapus "${serial}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#B05B3B',
                cancelButtonColor: '#D79771',
                reverseButtons: true
            }).then(async result => {
                if (result.isConfirmed) {
                    const res = await fetch(`/admin/serial/${id}`, {
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

        //   Klik untuk salin serial
        document.querySelectorAll('.serial-cell').forEach(cell => {
            cell.addEventListener('click', () => {
                const serial = cell.innerText.trim();
                navigator.clipboard.writeText(serial);
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Serial disalin!',
                    text: serial,
                    timer: 1200,
                    showConfirmButton: false
                });
            });
        });

        //   Perpanjang Serial
        function perpanjangSerial(id) {
            fetch(`/admin/serial/${id}/edit`)
                .then(res => res.json())
                .then(result => {
                    if (result.success) {
                        const s = result.data;
                        document.getElementById("extendId").value = s.id;
                        document.getElementById("extendSerial").value = s.serial;
                        document.getElementById("extendExpired").value = s.expired_at ?
                            new Date(s.expired_at).toLocaleDateString('id-ID') :
                            '-';
                        document.getElementById("extendMonths").value = "";
                        document.getElementById("extendNewExpired").value = "";
                        new bootstrap.Modal(document.getElementById("modalExtend")).show();
                    } else notifError(result.message);
                })
                .catch(err => notifError(err.message));
        }

        //   Update perkiraan tanggal baru saat user pilih bulan
        document.getElementById("extendMonths").addEventListener("change", function() {
            const current = document.getElementById("extendExpired").value;
            if (current === '-' || !current) return;
            const date = new Date(current.split('/').reverse().join('-'));
            const months = parseInt(this.value);
            if (!isNaN(months)) {
                date.setMonth(date.getMonth() + months);
                document.getElementById("extendNewExpired").value = date.toLocaleDateString('id-ID');
            }
        });

        //   Submit perpanjangan
        document.getElementById("formExtend").addEventListener("submit", async function(e) {
            e.preventDefault();
            const id = document.getElementById("extendId").value;
            const btn = this.querySelector("button[type='submit']");
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
            try {
                const res = await fetch(`/admin/serial/${id}/extend`, {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        extend_months: document.getElementById("extendMonths").value
                    })
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
            btn.innerHTML = 'Simpan Perpanjangan';
        });

        function openEmailModal(id) {
            document.getElementById('email_serial_id').value = id;
            var modal = new bootstrap.Modal(document.getElementById('emailModal'));
            modal.show();
        }

        async function kirimEmail() {
            let id = document.getElementById('email_serial_id').value;
            let email = document.getElementById('email_to').value;

            const btn = document.querySelector("#emailModal .modal-footer button.btn-add");

            // Efek loading
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mengirim...';

            try {
                const res = await fetch("{{ route('admin.serial.kirim_email') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        serial_id: id,
                        email: email
                    })
                });

                const data = await res.json();

                if (data.success) {
                    Swal.fire("Berhasil", "Email berhasil dikirim!", "success");
                    setTimeout(() => location.reload(), 800);
                } else {
                    Swal.fire("Gagal", data.message, "error");
                }
            } catch (err) {
                Swal.fire("Error", "Terjadi kesalahan pada server", "error");
            }

            // Reset tombol
            btn.disabled = false;
            btn.innerHTML = 'Kirim';
        }

        let targetForm = ""; // untuk mengetahui form yang sedang diisi (tambah/edit)

        // === BUKA POPUP PILIH USER ===
        function openUserPopup(target) {
            targetForm = target;
            document.getElementById("searchUserInput").value = "";
            filterUserList("");
            bootstrap.Modal.getOrCreateInstance(document.getElementById("modalPilihUser")).show();
        }

        // === FILTER LIST USER REALTIME ===
        document.getElementById("searchUserInput").addEventListener("keyup", function() {
            filterUserList(this.value.toLowerCase());
        });

        function filterUserList(keyword) {
            document.querySelectorAll("#userTablePopup .user-item").forEach(item => {
                const name = item.dataset.name.toLowerCase();
                const username = item.dataset.username.toLowerCase();
                const email = item.dataset.email.toLowerCase();

                item.style.display =
                    name.includes(keyword) ||
                    username.includes(keyword) ||
                    email.includes(keyword) ?
                    "" :
                    "none";
            });
        }


        // === PILIH USER ===
        function pilihUser(id, name) {
            if (targetForm === "tambah") {
                document.getElementById("tambahUserId").value = id;
                document.getElementById("tambahUserName").value = name;
            } else if (targetForm === "edit") {
                document.getElementById("editUserId").value = id;
                document.getElementById("editUserName").value = name;
            }

            bootstrap.Modal.getInstance(document.getElementById("modalPilihUser")).hide();
            targetForm = "";
        }
    </script>
@endsection
