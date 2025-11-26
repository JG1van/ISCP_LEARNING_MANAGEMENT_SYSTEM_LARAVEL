<ul class="list-unstyled m-0">

    {{-- 0. Dashboard --}}
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

    <li class="{{ request()->routeIs('admin.kelas.*') ? 'active' : '' }}">
        <a href="{{ route('admin.kelas.index') }}" class="d-block py-2 px-3 text-white">
            <i class="bi bi-people-fill me-2"></i> Kelas
        </a>
    </li>

    <li class="{{ request()->routeIs('admin.pengaduan.*') ? 'active' : '' }}">
        <a href="{{ route('admin.pengaduan.index') }}" class="d-block py-2 px-3 text-white">
            <i class="bi bi-chat-left-text me-2"></i> Pengaduan
        </a>
    </li>

    <li
        class="{{ request()->routeIs('admin.pengaturan.*') || request()->routeIs('admin.guru.*') || request()->routeIs('admin.siswa.*') || request()->routeIs('admin.admin.*') || request()->routeIs('admin.profil.*') || request()->routeIs('admin.pra_latihan.*') || request()->routeIs('admin.kategori_pengaduan.*') ? 'active' : '' }}">
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
</ul>
