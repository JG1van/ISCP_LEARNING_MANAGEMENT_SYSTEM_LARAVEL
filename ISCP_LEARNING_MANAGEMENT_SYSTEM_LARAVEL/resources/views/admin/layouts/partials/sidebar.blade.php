<nav>
    {{-- === Sidebar Offcanvas (Mobile) === --}}
    <div class="offcanvas offcanvas-start bg-dark text-white" tabindex="-1" id="sidebarMobile">
        <div class="offcanvas-header flex-column">

            <div class="d-flex justify-content-between w-100 align-items-center">
                <h5 class="offcanvas-title mb-0">Menu</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                    aria-label="Close"></button>
            </div>
        </div>

        <div class="offcanvas-body p-0 text-start">
            @include('admin.layouts.partials.sidebar-menu')
        </div>
    </div>

    {{-- === Sidebar Desktop === --}}
    <div class="sidebar d-none d-md-block text-white text-start">
        <div class="logo-container mb-1">
            <img src="{{ asset('images/logo.webp') }}" alt="Logo">
        </div>

        @include('admin.layouts.partials.sidebar-menu')
    </div>

</nav>
