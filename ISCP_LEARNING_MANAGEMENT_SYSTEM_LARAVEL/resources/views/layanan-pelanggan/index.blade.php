<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sci Media Online | Layanan Pelanggan </title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* === VARIABEL WARNA === */
        :root {
            --warna0: white;
            --warna1: black;
            --warna2: #753422;
            /* Dark Brown (Background Base) */
            --warna3: #B05B3B;
            /* Rust */
            --warna4: #D79771;
            /* Light Brown */
            --warna6: #FFEBC9;
            /* Parchment Lighter */
            --warna5: #FFF4E0;
            /* Parchment Base */

            --seal-red: #C0392B;
            --seal-gold: #D4AF37;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Times New Roman', serif;
            font-size: 17px;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        /* === BACKGROUND === */
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Times New Roman', serif;
            /* Medieval Serif Font */
            color: var(--warna2);
            position: relative;
            overflow-x: hidden;
            margin: 0;
        }

        /* Texture Layer (Stone Wall) */
        .bg-texture {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            pointer-events: none;
            /* Ensures clicks pass through to content */


        }

        /* Brick container layer */
        #wall {
            background-color: var(--warna2);
            position: fixed;
            top: 0;
            left: 0;
            width: 120vw;
            height: 120vh;
            display: flex;
            flex-wrap: wrap;
            align-content: flex-start;
            z-index: -2;
            /* Dibawah efek body::before & body::after */
            pointer-events: none;
        }

        .brick {
            background-color: #8a4b38;
            box-sizing: border-box;
        }

        /* === TYPOGRAPHY === */
        .main-title {
            color: var(--warna0);
            text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.9);
            font-weight: bold;
            letter-spacing: 1px;
            z-index: 99;
        }

        .intro-text {
            color: var(--warna0);
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.9);
            margin-bottom: 3rem;
            font-size: 1.1rem;
        }

        /* === ENVELOPE COMPONENT === */
        .envelope-wrapper {
            display: flex;
            justify-content: center;
            text-decoration: none;
            /* Remove underline from anchor */
        }

        .envelope {
            width: 330px;
            height: 220px;
            /* Gradient for parchment texture effect */
            background: linear-gradient(135deg, var(--warna5) 0%, var(--warna6) 100%);
            border-radius: 10px;
            /* Soft radius as requested */
            position: relative;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-end;
            /* Push text to bottom half */
            padding-bottom: 40px;
        }

        /* Hover Effect */
        .envelope:hover {
            transform: translateY(-25px);
            box-shadow: 0 20px 35px rgba(0, 0, 0, 0.6);
        }

        /* Envelope Flap (Triangle) */
        .envelope::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 120px;
            background-color: var(--warna3);
            clip-path: polygon(0 0, 100% 0, 50% 100%);
            z-index: 2;
            filter: drop-shadow(0 4px 4px rgba(0, 0, 0, 0.1));
        }

        /* Shadow/Depth for the fold */
        .envelope::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0.05), transparent 40%);
            pointer-events: none;
            z-index: 1;
            border-radius: 10px;
        }

        /* === WAX SEAL === */
        .wax-seal {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            position: absolute;
            top: 90px;
            /* Position at the tip of the flap */
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 3;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            /* Subtle 3D shadow */
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid rgba(255, 255, 255, 0.15);
        }

        /* Seal Colors */
        .seal-red {
            background: radial-gradient(circle at 30% 30%, #ff7675, var(--seal-red));
            background-color: var(--seal-red);
        }

        .seal-gold {
            background: radial-gradient(circle at 30% 30%, #ffeaa7, var(--seal-gold));
            background-color: var(--seal-gold);
        }

        /* Inner Seal Ring */
        .wax-seal::after {
            content: '';
            width: 34px;
            height: 34px;
            border-radius: 50%;
            border: 1px dashed rgba(0, 0, 0, 0.2);
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.2);
        }

        /* === TEXT LABEL === */
        .envelope-label {
            z-index: 4;
            /* Above everything */
            font-size: 1.25rem;
            font-weight: bold;
            color: var(--warna2);
            text-transform: uppercase;
            letter-spacing: 1px;
            text-align: center;
            margin-top: 20px;
            text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
        }

        /* Mobile Adjustment */
        @media (max-width: 768px) {
            .envelope {
                width: 100%;
                max-width: 300px;
            }
        }
    </style>
</head>

<body>
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
    @if (session('notif-success'))
        <div class="alert custom-alert-1 alert-dismissible fade show mx-3" role="alert">
            {{ session('notif-success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('notif-error'))
        <div class="alert custom-alert-1 alert-dismissible fade show mx-3" role="alert">
            {{ session('notif-error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('success_html'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                html: `{!! session('success_html') !!}`,
                showConfirmButton: false,
                timer: 5000
            })
        </script>
    @endif
    <div id="wall"></div>
    <!-- Background Texture Overlay -->
    <div class="bg-texture"></div>

    <div class="container py-5"
        style='background: url("{{ asset('images/bg-3.svg') }}") center / cover no-repeat;  background-size: 390px auto;'>

        <div class="text-center fade-in">
            <h3 class="main-title">PUSAT LAYANAN PELANGGAN SCI MEDIA</h3>
            <h5 class="main-title mb-5">Unit Layanan Bantuan • Penanganan Pengaduan • Dukungan Pelanggan</h5>
            <p class="intro-text">
                Mengalami kendala atau membutuhkan bantuan? Kami hadir untuk membantu Anda kapan saja.
                Melalui Layanan Pelanggan SCI Media, Anda dapat menemukan solusi cepat dari daftar panduan yang
                tersedia,
                atau langsung berkomunikasi dengan Customer Service (CS) melalui fitur pesan.
                Pilih menu di bawah ini untuk memulai layanan baru atau melanjutkan layanan yang sebelumnya.
            </p>
        </div>


        <div class="row justify-content-center gy-5">

            <!-- MENU 1: MULAI LAYANAN -->
            <div class="col-md-5 col-lg-4 mx-5">
                <a href="/layanan-pelanggan-pelapor/create" class="envelope-wrapper">
                    <div class="envelope">
                        <div class="wax-seal seal-red"></div>
                        <div class="envelope-label">
                            Mulai
                        </div>
                    </div>
                </a>
            </div>

            <!-- MENU 2: LANJUT LAYANAN -->
            <div class="col-md-5 col-lg-4 mx-5">
                <a href="#" class="envelope-wrapper" id="btnLanjutPengaduan">
                    <div class="envelope">
                        <div class="wax-seal seal-gold"></div>
                        <div class="envelope-label">
                            Lanjut
                        </div>
                    </div>
                </a>
            </div>

        </div>

    </div>


    <!-- === JAVASCRIPT === -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const btnLanjut = document.getElementById("btnLanjutPengaduan");

            btnLanjut.addEventListener("click", function(e) {
                e.preventDefault();

                Swal.fire({
                    title: "Masukkan Kode Layanan Pelanggan ",
                    input: "text",
                    inputPlaceholder: "Contoh: 1234-5678-ABCD-EFGH",
                    showCancelButton: true,
                    confirmButtonText: "Lanjutkan",
                    cancelButtonText: "Batal",
                    confirmButtonColor: '#B05B3B',
                    cancelButtonColor: '#D79771',
                    background: '#FFF4E0',
                    color: '#753422',

                    inputValidator: (value) => {
                        if (!value) return "Kode tidak boleh kosong!";
                    }
                }).then((result) => {
                    if (result.isConfirmed) {

                        // === FORM SUBMIT ASLI ===
                        let form = document.createElement("form");
                        form.method = "POST";
                        form.action = "{{ route('layanan-pelanggan.continue') }}";

                        let csrf = document.createElement("input");
                        csrf.type = "hidden";
                        csrf.name = "_token";
                        csrf.value = "{{ csrf_token() }}";

                        let input = document.createElement("input");
                        input.type = "hidden";
                        input.name = "room_code";
                        input.value = result.value;

                        form.appendChild(csrf);
                        form.appendChild(input);
                        document.body.appendChild(form);

                        form.submit();
                    }
                });
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const wall = document.getElementById("wall");

            const rows = 15;
            const bricksPerRow = 12;

            const colors = [
                "#8a4b38",
                "#953a2d",
                "#9e5e4b",
                "#933125",
                "#7d4436",
            ];

            const totalBricks = Math.floor(rows * bricksPerRow * 1.5);

            for (let i = 0; i < totalBricks; i++) {
                const brick = document.createElement("div");
                brick.classList.add("brick");

                // Ukuran acak
                const width = Math.floor(Math.random() * 60) + 80;
                const height = Math.floor(Math.random() * 10) + 55;
                brick.style.width = `${width}px`;
                brick.style.height = `${height}px`;

                // Warna acak
                brick.style.backgroundColor =
                    colors[Math.floor(Math.random() * colors.length)];

                // Border-radius acak
                const b1 = Math.floor(Math.random() * 10) + 2;
                const b2 = Math.floor(Math.random() * 10) + 2;
                const b3 = Math.floor(Math.random() * 10) + 2;
                const b4 = Math.floor(Math.random() * 10) + 2;
                brick.style.borderRadius = `${b1}px ${b2}px ${b3}px ${b4}px`;

                // Rotasi acak
                const rotation = (Math.random() * 2 - 1).toFixed(1);
                brick.style.transform = `rotate(${rotation}deg)`;

                // Margin acak
                const marginX = Math.floor(Math.random() * 4) + 1;
                const marginY = Math.floor(Math.random() * 4) + 1;
                brick.style.margin = `${marginY}px ${marginX}px`;

                wall.appendChild(brick);
            }
        });
    </script>

</body>

</html>
