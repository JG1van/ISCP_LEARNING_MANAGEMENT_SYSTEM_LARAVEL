{{-- <ul class="list-unstyled m-0">
    <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <a href="{{ route('admin.dashboard') }}" class="d-block py-2 px-3 text-white">
            <i class="bi bi-speedometer2 me-2"></i> Dashboard
        </a>
    </li>
    <li class="{{ request()->routeIs('admin.mapel.*') ? 'active' : '' }}">
        <a href="{{ route('admin.mapel.index') }}" class="d-block py-2 px-3 text-white">
            <i class="bi bi-book me-2"></i> Mata Pelajaran
        </a>
    </li>
    <li class="{{ request()->routeIs('admin.pelajaran.*') ? 'active' : '' }}">
        <a href="{{ route('admin.pelajaran.index') }}" class="d-block py-2 px-3 text-white">
            <i class="bi bi-journal-text me-2"></i> Pelajaran
        </a>
    </li>
    <li class="{{ request()->routeIs('admin.produk.*') ? 'active' : '' }}">
        <a href="{{ route('admin.produk.index') }}" class="d-block py-2 px-3 text-white">
            <i class="bi bi-box-seam me-2"></i> Produk
        </a>
    </li>
    <li class="{{ request()->routeIs('admin.serial.*') ? 'active' : '' }}">
        <a href="{{ route('admin.serial.index') }}" class="d-block py-2 px-3 text-white">
            <i class="bi bi-key me-2"></i> Serial
        </a>
    </li>
    <li class="{{ request()->routeIs('admin.guru.*') ? 'active' : '' }}">
        <a href="{{ route('admin.guru.index') }}" class="d-block py-2 px-3 text-white">
            <i class="fas fa-chalkboard-teacher me-2"></i> Guru
        </a>
    </li>
    <li class="{{ request()->routeIs('admin.layanan-pelanggan.*') ? 'active' : '' }}">
        <a href="{{ route('admin.layanan-pelanggan.index') }}" class="d-block py-2 px-3 text-white">
            <i class="bi bi-chat-left-text me-2"></i> Layanan Pelanggan
        </a>
    </li>

    <li
        class="{{ request()->routeIs('admin.pengaturan.*') ||
        request()->routeIs('admin.admin.*') ||
        request()->routeIs('admin.siswa.*') ||
        request()->routeIs('admin.profil.*') ||
        request()->routeIs('admin.pra-latihan.*') ||
        request()->routeIs('admin.kategori_pertanyaan.*') ||
        request()->routeIs('admin.kelas.*')
            ? 'active'
            : '' }}">
        <a href="{{ route('admin.pengaturan.index') }}" class="d-block py-2 px-3 text-white">
            <i class="bi bi-gear-fill me-2"></i> Pengaturan
        </a>
    </li>

    <li class="mt-3 border-top border-secondary pt-3">
        <form action="{{ route('logout') }}" method="POST" class="p-0">
            @csrf
            <button type="submit" class="d-block py-2 px-3 w-100 text-white text-start bg-transparent border-0">
                <i class="fas fa-sign-out-alt me-2"></i> Keluar
            </button>
        </form>
    </li>

</ul> --}}

@php
    // ROLE USER SEKARANG
    $role = $userRole ?? (Auth::user()->role_id ?? null);

    // DEFINISI ROLE PER MENU (sesuai tabel kamu)
    $menus = [
        // Dashboard
        [
            'route' => 'admin.dashboard',
            'match' => 'admin.dashboard',
            'label' => 'Dashboard',
            'icon' => 'bi bi-speedometer2',
            'roles' => [1, 2, 3, 4, 5],
        ],

        // Mata Pelajaran
        [
            'route' => 'admin.mapel.index',
            'match' => 'admin.mapel.*',
            'label' => 'Mata Pelajaran',
            'icon' => 'bi bi-book',
            'roles' => [1, 2, 4],
        ],

        // Pelajaran
        [
            'route' => 'admin.pelajaran.index',
            'match' => 'admin.pelajaran.*',
            'label' => 'Pelajaran',
            'icon' => 'bi bi-journal-text',
            'roles' => [1, 2, 4],
        ],

        // Produk
        [
            'route' => 'admin.produk.index',
            'match' => 'admin.produk.*',
            'label' => 'Produk',
            'icon' => 'bi bi-box-seam',
            'roles' => [1, 2, 3],
        ],

        // Serial
        [
            'route' => 'admin.serial.index',
            'match' => 'admin.serial.*',
            'label' => 'Serial',
            'icon' => 'bi bi-key',
            'roles' => [1, 2, 3],
        ],

        // Guru
        [
            'route' => 'admin.guru.index',
            'match' => 'admin.guru.*',
            'label' => 'Guru',
            'icon' => 'fas fa-chalkboard-teacher',
            'roles' => [1, 2, 3],
        ],

        // Layanan Pelanggan (Complaint)
        [
            'route' => 'admin.layanan-pelanggan.index',
            'match' => 'admin.layanan-pelanggan.*',
            'label' => 'Layanan Pelanggan',
            'icon' => 'bi bi-chat-left-text',
            'roles' => [1, 2, 5],
        ],

        // Pengaturan
        [
            'route' => 'admin.pengaturan.index',
            'match' => [
                'admin.pengaturan.*',
                'admin.admin.*',
                'admin.siswa.*',
                'admin.profil.*',
                'admin.pra-latihan.*',
                'admin.kategori_pertanyaan.*',
                'admin.kelas.*',
            ],
            'label' => 'Pengaturan',
            'icon' => 'bi bi-gear-fill',
            'roles' => [0, 1, 2, 3, 4, 5],
        ],
    ];
@endphp


<ul class="list-unstyled m-0">

    {{-- LOOP MENU --}}
    @foreach ($menus as $m)
        @php
            $isActive = request()->routeIs($m['match']);
            $allowed = in_array($role, $m['roles']);
        @endphp

        <li class="{{ $isActive ? 'active' : '' }} {{ !$allowed ? 'disabled-li' : '' }}">

            @if ($allowed)
                {{-- MENU NORMAL --}}
                <a href="{{ route($m['route']) }}" class="d-block py-2 px-3 text-white">
                    <i class="{{ $m['icon'] }} me-2"></i> {{ $m['label'] }}
                </a>
            @else
                {{-- MENU TERKUNCI --}}
                <a class="disabled-menu d-block">
                    <i class="bi bi-lock-fill me-2"></i> {{ $m['label'] }}
                </a>
            @endif

        </li>
    @endforeach


    {{-- LOGOUT --}}
    <li class="mt-3 border-top border-secondary pt-3">
        <form action="{{ route('logout') }}" method="POST" class="p-0">
            @csrf
            <button type="submit" class="d-block py-2 px-3 w-100 text-white text-start bg-transparent border-0">
                <i class="fas fa-sign-out-alt me-2"></i> Keluar
            </button>
        </form>
    </li>

</ul>
