<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Castle Gate Login | SciMedia Online</title>

    <!-- Optional Bootstrap for basic layout/reset -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --stone-dark: #2a2a2a;
            --stone-light: #6C6C6C;
            --stone-base: #4B4B4B;
            --wood-dark: #2d1a0e;
            --wood-light: #5C3B1E;
            --metal: #C8C8C8;
            --metal-dark: #555;
            --fire-core: #fff;
            --fire-inner: #FFCC6A;
            --fire-outer: #E39B39;
            --parchment: #f4e4bc;
            --wax-red: #8a2323;
        }

        body {
            margin: 0;
            min-height: 100vh;
            height: auto;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #1a1a1a;
            /* Stone Wall Texture Pattern */
            background-image:
                linear-gradient(335deg, rgba(0, 0, 0, 0.3) 23px, transparent 23px),
                linear-gradient(155deg, rgba(0, 0, 0, 0.3) 23px, transparent 23px),
                linear-gradient(335deg, rgba(0, 0, 0, 0.3) 23px, transparent 23px),
                linear-gradient(155deg, rgba(0, 0, 0, 0.3) 23px, transparent 23px);
            background-size: 58px 58px;
            background-position: 0px 2px, 4px 35px, 29px 31px, 34px 6px;
            font-family: 'Times New Roman';
            overflow: hidden;
            perspective: 1500px;
        }

        /* Ambient Fog */
        body::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 40%;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
            pointer-events: none;
            z-index: -1;
        }

        /* ---------------- CASTLE GATE STRUCTURE ---------------- */
        .castle-gate-container {
            position: relative;
            width: 460px;
            height: 550px;
            display: flex;
            justify-content: center;
            align-items: flex-end;
        }

        /* Stone Arch Frame */
        .stone-arch {
            position: absolute;
            inset: -20px;
            border-radius: 240px 240px 10px 10px;
            background:
                radial-gradient(circle at 50% 0%, transparent 60%, #1a1a1a 100%),
                repeating-linear-gradient(45deg, var(--stone-base), var(--stone-base) 10px, var(--stone-light) 12px, var(--stone-light) 20px);
            box-shadow:
                inset 0 0 30px #000,
                0 10px 30px rgba(0, 0, 0, 0.8);
            z-index: 10;
            pointer-events: none;
            /* Let clicks pass through to door */
            border: 8px solid #333;
            border-bottom: none;
        }

        .door-frame {
            position: relative;
            width: 100%;
            height: 100%;
            border-radius: 220px 220px 5px 5px;
            perspective: 1200px;
            z-index: 99;
        }

        /* Door Panels */
        .door-panel {
            position: absolute;
            top: 0;
            width: 50%;
            height: 100%;
            background: var(--wood-light);
            /* Realistic Wood Texture */
            background-image: repeating-linear-gradient(90deg, rgba(0, 0, 0, 0.1) 0px, rgba(0, 0, 0, 0.1) 2px, transparent 2px, transparent 4px),
                linear-gradient(to bottom, rgba(0, 0, 0, 0.5), transparent, rgba(0, 0, 0, 0.8));
            background-size: 100% 100%, 100% 100%;
            transform-style: preserve-3d;
            transition: transform 1.5s cubic-bezier(0.25, 1, 0.5, 1);
            box-shadow: inset 0 0 20px rgba(0, 0, 0, 0.8);
            display: flex;
            flex-direction: column;
            justify-content: space-around;
            align-items: center;
            border: 2px solid #1a1a1a;
        }

        .door-panel.left {
            left: 0;
            transform-origin: left center;
            border-radius: 220px 0 5px 5px;
            border-right: 1px solid #1a1a1a;
        }

        .door-panel.right {
            right: 0;
            transform-origin: right center;
            border-radius: 0 220px 5px 5px;
            border-left: 1px solid #1a1a1a;
        }


        /* Metal Decorations (Studs & Hinges) */
        .metal-band {
            width: 100%;
            height: 40px;
            background: linear-gradient(to bottom, #444, #888, #444);
            position: relative;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);
        }

        .stud {
            width: 12px;
            height: 12px;
            background: radial-gradient(circle at 30% 30%, var(--metal), var(--metal-dark));
            border-radius: 50%;
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            box-shadow: 1px 1px 2px rgba(0, 0, 0, 0.8);
        }

        /* Ring Handles */
        .handle-ring {
            width: 40px;
            height: 40px;
            border: 6px solid #444;
            border-radius: 50%;
            position: absolute;
            top: 55%;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.6);
            background: transparent;
        }

        .door-panel.left .handle-ring {
            right: 20px;
            border-left-color: #666;
        }

        .door-panel.right .handle-ring {
            left: 20px;
            border-right-color: #666;
        }

        /* Open State */
        .castle-gate-container.open .door-panel.left {
            transform: rotateY(-110deg);
        }

        .castle-gate-container.open .door-panel.right {
            transform: rotateY(110deg);
        }

        /* ---------------- INTERIOR (LOGIN FORM) ---------------- */
        .interior {
            position: absolute;
            inset: 10px;
            background: #000;
            border-radius: 210px 210px 5px 5px;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            box-shadow: inset 0 0 100px #000;
        }

        .interior::after {
            content: "";
            position: absolute;
            inset: 0;
            background: url("{{ asset('images/bg-2.svg') }}") center / cover no-repeat;
            filter: blur(6px);
            transform: scale(1.1);
            opacity: 0.9;
            z-index: 99;
        }

        /* Torch Light Glow inside */
        .interior::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(227, 155, 57, 0.2) 0%, transparent 70%);
            animation: flicker 3s infinite alternate;
        }

        .swal2-container {
            position: fixed !important;
        }

        .scroll-paper {
            margin-top: 100px;
            position: relative;
            width: 80%;
            background: var(--parchment);
            padding: 40px 30px;
            border-radius: 5px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.8), inset 0 0 30px rgba(139, 69, 19, 0.2);
            text-align: center;
            transform: scale(0.8) translateY(20px);
            opacity: 0;
            transition: all 1s ease 0.5s;
            border: 1px solid #d4c4a0;
            z-index: 999;
        }

        .castle-gate-container.open .scroll-paper {
            transform: scale(1) translateY(0);
            opacity: 1;
        }

        /* Shield Logo */
        .shield-logo {
            width: 80px;
            height: 90px;
            background: linear-gradient(135deg, #333, #111);
            margin: 0 auto 20px;
            clip-path: polygon(50% 0, 100% 20%, 100% 80%, 50% 100%, 0 80%, 0 20%);
            display: flex;
            justify-content: center;
            align-items: center;
            border: 2px solid var(--metal);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
            cursor: pointer;
            transition: transform 0.3s;
        }

        .shield-logo:hover {
            transform: scale(1.05);
        }

        .shield-logo img {
            width: 60%;
            opacity: 0.8;
        }

        /* Typography */
        h2 {
            color: #3e2723;
            font-family: 'Cinzel', serif;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 25px;
            border-bottom: 2px solid #3e2723;
            display: inline-block;
            padding-bottom: 5px;
        }

        /* Inputs */
        .medieval-input {
            background: transparent;
            border: none;
            border-bottom: 2px solid #8b4513;
            border-radius: 0;
            font-family: 'Times New Roman', serif;
            font-size: 1.1rem;
            color: #3e2723;
            box-shadow: none !important;
            padding: 10px;
            margin-bottom: 20px;
            transition: border-color 0.3s;
        }

        .medieval-input:focus {
            background: rgba(139, 69, 19, 0.05);
            border-color: var(--wax-red);
        }

        .medieval-input::placeholder {
            color: #8b7e75;
            font-style: italic;
        }

        /* Wax Seal Button */
        .btn-wax {
            background: radial-gradient(circle at 30% 30%, #a83232, #6d1a1a);
            color: #fff;
            font-family: 'Cinzel', serif;
            border: none;
            width: 100%;
            padding: 12px;
            border-radius: 5px;
            box-shadow:
                0 4px 0 #4a1212,
                0 5px 10px rgba(0, 0, 0, 0.4);
            position: relative;
            transition: all 0.2s;
            text-transform: uppercase;
            letter-spacing: 1px;
            z-index: 999;
        }

        .btn-wax:hover {
            transform: translateY(2px);
            box-shadow:
                0 2px 0 #4a1212,
                0 3px 5px rgba(0, 0, 0, 0.4);
            background: radial-gradient(circle at 30% 30%, #b93a3a, #7e1e1e);
            color: #ffebc9;
        }

        .btn-wax:active {
            transform: translateY(4px);
            box-shadow: none;
        }

        /* ---------------- TORCHES & PARTICLES ---------------- */
        .torch {
            position: absolute;
            top: 200px;
            width: 20px;
            height: 60px;
            background: #333;
            z-index: 20;
        }

        .torch.left {
            left: -60px;
        }

        .torch.right {
            right: -60px;
        }

        .torch::before {
            /* Holder */
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 10px;
            height: 30px;
            background: #555;
            transform: translateX(-50%) rotate(45deg);
        }

        .flame {
            position: absolute;
            top: -30px;
            left: 50%;
            transform: translateX(-50%);
            width: 20px;
            height: 28px;
            background: var(--fire-outer);
            border-radius: 50% 50% 40% 40%;
            /* arah api vertikal */
            box-shadow: 0 0 20px var(--fire-outer), 0 0 40px var(--fire-inner);
            animation: burn 0.5s infinite alternate;
            transform-origin: center bottom;
        }


        @keyframes burn {
            0% {
                transform: translate(-50%, 0) scale(1) rotate(-2deg);
                opacity: 0.85;
            }

            50% {
                transform: translate(-50%, -10px) scale(1.15) rotate(2deg);
                opacity: 1;
            }

            100% {
                transform: translate(-50%, -20px) scale(1.25) rotate(-1deg);
                opacity: 0.9;
            }
        }


        @keyframes flicker {
            0% {
                opacity: 0.8;
            }

            100% {
                opacity: 0.5;
            }
        }

        /* Dust Particles */
        .particle {
            position: absolute;
            background: rgba(200, 190, 170, 0.6);
            border-radius: 50%;
            pointer-events: none;
            z-index: 30;
            animation: floatUp 1.5s ease-out forwards;
        }

        @keyframes floatUp {
            0% {
                transform: translateY(0) scale(1);
                opacity: 0;
            }

            20% {
                opacity: 0.8;
            }

            100% {
                transform: translateY(-50px) scale(2);
                opacity: 0;
            }
        }

        /* ========= RESPONSIVE MODE ========= */
        @media (max-width: 768px) {

            /* Hilangkan pintu */
            .door-frame,
            .stone-arch,
            .torch {
                display: none !important;
            }

            /* Kontainer diperkecil biar pas layar hp/tablet */
            .castle-gate-container {
                width: 100%;
                max-width: 400px;
                height: auto;
                margin-top: 40px;
            }

            /* Interior langsung tampil clean */
            .interior {
                border-radius: 20px;
                inset: 0;
                box-shadow: none;
            }

            /* Scroll-paper tampil normal */
            .scroll-paper {
                margin-top: 0;
                transform: scale(1) translateY(0) !important;
                opacity: 1 !important;
            }

            /* Matikan animasi open */
            .castle-gate-container .door-panel {
                transform: none !important;
            }
        }

        @media (max-width: 768px) {

            .door-panel,
            .door-frame,
            .stone-arch,
            .torch {
                display: none !important;
                pointer-events: none !important;
                /* aman */
            }

            .castle-gate-container {
                width: 100% !important;
                height: auto !important;
                padding: 20px;
                display: flex;
                justify-content: center;
                align-items: flex-start;
            }

            .interior {
                position: relative;
                inset: 0;
                width: 100%;
                border-radius: 10px;
                background: rgba(0, 0, 0, 0.7);
                padding: 20px;
                box-shadow: none;
            }

            .scroll-paper {
                margin-top: 0 !important;
                transform: scale(1) translateY(0) !important;
                opacity: 1 !important;
                width: 100% !important;
            }

            /* HAPUS pointer-events untuk castleGate */
        }

        .scroll-paper.hide {
            transform: scale(0.8) translateY(30px);
            opacity: 0;
            transition: all 0.6s ease;
        }
    </style>
</head>

<body>
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


    <div class="castle-gate-container" id="castleGate">

        <!-- Decorative Torches -->
        <div class="torch left">
            <div class="flame"></div>
        </div>
        <div class="torch right">
            <div class="flame"></div>
        </div>

        <!-- Stone Arch Overlay -->
        <div class="stone-arch"></div>

        <!-- The Interior (Form) -->
        <div class="interior">
            <div class="scroll-paper">
                <div class="shield-logo" id="closeBtn" title="Close Gate">
                    <!-- Simple SVG Shield Icon -->
                    <img src="{{ asset('images/logo.webp') }}" alt="Logo" id="closeDoor">
                </div>
                <h2>LOGIN</h2>

                <form method="POST" action="{{ route('login.process') }}" autocomplete="off">
                    @csrf
                    <input type="text" name="username" class="form-control mb-3" placeholder="Username" required>
                    <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>
                    <button class="btn-wax">Login</button>
                </form>
            </div>
        </div>

        <!-- The Doors -->
        <div class="door-frame">
            <div class="door-panel left">
                <div class="metal-band" style="top: 15%">
                    <div class="stud" style="left: 10%"></div>
                    <div class="stud" style="left: 50%"></div>
                    <div class="stud" style="left: 90%"></div>
                </div>
                <div class="handle-ring"></div>
                <div class="metal-band" style="bottom: 1%">
                    <div class="stud" style="left: 10%"></div>
                    <div class="stud" style="left: 50%"></div>
                    <div class="stud" style="left: 90%"></div>
                </div>
            </div>
            <div class="door-panel right">
                <div class="metal-band" style="top: 15%">
                    <div class="stud" style="left: 10%"></div>
                    <div class="stud" style="left: 50%"></div>
                    <div class="stud" style="left: 90%"></div>
                </div>
                <div class="handle-ring"></div>
                <div class="metal-band" style="bottom: 1%">
                    <div class="stud" style="left: 10%"></div>
                    <div class="stud" style="left: 50%"></div>
                    <div class="stud" style="left: 90%"></div>
                </div>
            </div>
        </div>

    </div>

    <!-- JS Logic -->
    <script>
        const gate = document.getElementById("castleGate");
        const closeBtn = document.getElementById("closeBtn");
        const scrollPaper = document.querySelector(".scroll-paper");
        let isOpen = false;

        function createParticles() {
            for (let i = 0; i < 20; i++) {
                const p = document.createElement('div');
                p.classList.add('particle');
                const x = (gate.offsetWidth / 2) + (Math.random() * 100 - 50);
                const y = gate.offsetHeight - 20;
                p.style.left = x + 'px';
                p.style.top = y + 'px';
                p.style.width = (Math.random() * 6 + 2) + 'px';
                p.style.height = p.style.width;
                gate.appendChild(p);
                setTimeout(() => p.remove(), 1500);
            }
        }

        function openGate() {
            if (!isOpen) {
                isOpen = true;
                gate.classList.add("open");
                createParticles();
            }
        }

        function closeGate(e) {
            e.stopPropagation();

            // animasi form keluar dulu
            scrollPaper.classList.add("hide");

            // tunggu 600ms baru pintu menutup
            setTimeout(() => {
                isOpen = false;
                gate.classList.remove("open");
                scrollPaper.classList.remove("hide"); // reset agar saat dibuka lagi normal
            }, 600);
        }

        gate.addEventListener("click", openGate);
        gate.addEventListener("mouseenter", openGate);

        closeBtn.addEventListener("click", closeGate);

        document.querySelectorAll("input, button").forEach(el => {
            el.addEventListener("click", e => e.stopPropagation());
        });
    </script>

</body>

</html>
