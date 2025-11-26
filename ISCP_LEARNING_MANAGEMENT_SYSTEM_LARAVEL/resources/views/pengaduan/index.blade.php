{{-- ================= NOTIFIKASI SWEETALERT ================= --}}
@if (session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: "{{ session('success') }}",
            showConfirmButton: false,
            timer: 2000
        })
    </script>
@endif

@if (session('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: "{{ session('error') }}",
            confirmButtonColor: '#B05B3B',
        })
    </script>
@endif

@if ($errors->any())
    <script>
        let errorMessages = `{!! implode('<br>', $errors->all()) !!}`;

        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            html: errorMessages,
        });
    </script>
@endif


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SciMedia Online | Pengaduan</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            background: #f2f2f2;
        }

        .menu-card {
            background: white;
            border-radius: 14px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 2px 6px rgba(0, 0, 0, .1);
            cursor: pointer;
            transition: .2s;
        }

        .menu-card:hover {
            transform: scale(1.03);
        }

        .btn-big {
            padding: 14px;
            font-size: 17px;
            font-weight: bold;
        }

        a.card-link {
            text-decoration: none;
            color: inherit;
        }
    </style>
</head>

<body>

    <div class="container py-5">

        <h3 class="fw-bold text-center mb-4">Layanan Pengaduan SciMedia</h3>

        <p class="text-center text-muted mb-4">
            Silakan pilih menu berikut untuk membuat atau melanjutkan pengaduan Anda.
        </p>

        <div class="row justify-content-center">

            <!-- BUAT PENGADUAN -->
            <div class="col-md-5 mb-3">
                <div class="menu-card">
                    <h5 class="mb-3">Buat Pengaduan Baru</h5>

                    <a href="{{ route('pengaduan.create') }}" class="btn btn-primary btn-big w-100">
                        Mulai Pengaduan
                    </a>
                </div>
            </div>

            <!-- LANJUTKAN PENGADUAN -->
            <div class="col-md-5 mb-3">
                <div class="menu-card" id="btnLanjutPengaduan">
                    <h5 class="mb-3">Lanjutkan Pengaduan</h5>
                    <button class="btn btn-success btn-big w-100">
                        Masukkan Kode
                    </button>
                </div>
            </div>

        </div>

    </div>

    <script>
        document.querySelector("#btnLanjutPengaduan button").addEventListener("click", function(e) {
            e.preventDefault(); // cegah pemicu default button

            Swal.fire({
                title: "Masukkan Kode Pengaduan",
                input: "text",
                inputPlaceholder: "Contoh: 1234-5678-ABCD-EFGH",
                showCancelButton: true,
                confirmButtonText: "Lanjutkan",
                cancelButtonText: "Batal",
                inputValidator: (value) => {
                    if (!value) {
                        return "Kode tidak boleh kosong!";
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {

                    // Buat form POST manual
                    let form = document.createElement("form");
                    form.method = "POST";
                    form.action = "{{ route('pengaduan.continue') }}";

                    let csrf = document.createElement("input");
                    csrf.type = "hidden";
                    csrf.name = "_token";
                    csrf.value = "{{ csrf_token() }}";

                    let input = document.createElement("input");
                    input.type = "hidden";
                    input.name = "complaint_code";
                    input.value = result.value;

                    form.appendChild(csrf);
                    form.appendChild(input);
                    document.body.appendChild(form);

                    form.submit(); // kirim form → route berjalan
                }
            });
        });
    </script>


</body>

</html>
