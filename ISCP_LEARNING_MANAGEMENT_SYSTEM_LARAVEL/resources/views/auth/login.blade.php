<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login | SciMedia Online</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --warna0: #ffffff;
            --warna1: #000000;
            --warna2: #753422;
            --warna3: #b05b3b;
            --warna4: #d79771;
            --warna5: #ffebc9;
        }

        body {
            margin: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(rgba(255, 244, 227, 0.7), rgba(255, 244, 227, 0.7)),
                url("{{ asset('images/bg-1.svg') }}") center / cover no-repeat;
            font-family: 'Times New Roman';
            overflow: hidden;
        }

        /* ---------------- PINTU ---------------- */
        .door {
            position: relative;
            width: 400px;
            height: 550px;
            perspective: 2000px;
            border-radius: 1000px 1000px 10px 10px;
            border: var(--warna2) 15px solid;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: .4s ease;
        }

        .door::before {
            content: "";
            position: absolute;
            inset: 0;
            background: url("{{ asset('images/bg-2.svg') }}") center / cover no-repeat;
            filter: blur(6px);
            transform: scale(1.1);
            opacity: 0.9;
            z-index: -1;
        }

        .door-cover {
            position: absolute;
            top: 0;
            width: 50%;
            height: 100%;
            background: var(--warna3);
            display: flex;
            align-items: center;
            justify-content: center;
            backface-visibility: hidden;
            transform-style: preserve-3d;
            transition: transform 1.3s cubic-bezier(0.19, 1, 0.22, 1);
            box-shadow: inset 0 0 25px rgba(255, 255, 255, 0.2);
            border: 1px solid var(--warna4);
        }

        .door-cover.left {
            left: 0;
            border-radius: 1000px 0 10px 10px;
            transform-origin: left center;
        }

        .door-cover.right {
            right: 0;
            border-radius: 0 1000px 10px 10px;
            transform-origin: right center;
        }

        /* Saat pintu dibuka */
        .door.open .door-cover.left {
            transform: rotateY(-100deg);
        }

        .door.open .door-cover.right {
            transform: rotateY(100deg);
        }

        /* Tombol (handle) */
        .door-cover .knob {
            position: absolute;
            top: 55%;
            width: 5px;
            height: 55px;
            background: var(--warna1);
            border-radius: 3px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .door-cover.left .knob {
            right: 25px;
        }

        .door-cover.right .knob {
            left: 25px;
        }

        /* ISI */
        .door-content {
            position: relative;
            text-align: center;
            width: 80%;
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 1s ease, transform 1s ease;
        }

        .door.open .door-content {
            opacity: 1;
            transform: translateY(0);
        }

        /* LOGO = tombol tutup */
        .door-content img {
            width: 120px;
            border-radius: 100%;
            cursor: pointer;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
            margin-bottom: 15px;
            opacity: 0;
            transform: scale(.7);
            transition: .8s ease .4s;
        }

        .door.open .door-content img {
            opacity: 1;
            transform: scale(1);
        }

        .btn-login {
            background: var(--warna3);
            color: var(--warna0);
            width: 100%;
            border: none;
            padding: 8px;
            font-weight: bold;
            border-radius: 6px;
        }

        .btn-login:hover {
            background: var(--warna4);
            color: var(--warna1);
        }
    </style>
</head>

<body>

    <div class="door" id="door">

        <div class="door-cover left">
            <div class="knob"></div>
        </div>

        <div class="door-cover right">
            <div class="knob"></div>
        </div>

        <div class="door-content">
            <img src="{{ asset('images/logo.webp') }}" alt="Logo" id="closeDoor">
            <h2 class="text-white mb-3">Login Admin</h2>

            <form method="POST" action="{{ route('login.process') }}" autocomplete="off">
                @csrf
                <input type="text" name="username" class="form-control mb-3" placeholder="Username" required>
                <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>
                <button class="btn-login">Login</button>
            </form>

        </div>

    </div>
    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Login Gagal',
                text: @json(session('error')),
                confirmButtonColor: '#B05B3B'
            });
        </script>
    @endif

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: @json(session('success')),
                confirmButtonColor: '#B05B3B'
            });
        </script>
    @endif

    @if ($errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                html: `{!! implode('<br>', $errors->all()) !!}`,
                confirmButtonColor: '#B05B3B'
            });
        </script>
    @endif


    <script>
        const door = document.getElementById("door");
        const logoClose = document.getElementById("closeDoor");

        let isOpen = false;

        /** BUKA PINTU */
        function openDoor() {
            if (!isOpen) {
                isOpen = true;
                door.classList.add("open");
            }
        }

        /** TUTUP PINTU (klik logo) */
        logoClose.addEventListener("click", (e) => {
            e.stopPropagation(); // <<< FIX UTAMA
            isOpen = false;
            door.classList.remove("open");
        });

        /* Buka saat klik pintu */
        door.addEventListener("click", (e) => {
            if (!isOpen) openDoor();
        });

        /* Buka saat hover */
        door.addEventListener("mouseenter", () => {
            if (!isOpen) openDoor();
        });
    </script>


</body>

</html>
