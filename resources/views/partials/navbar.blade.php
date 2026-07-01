<header class="navbar">
    <nav class="container">
        <div class="flex items-center" style="gap: 1.5rem;">
            <a href="{{ url('/') }}" class="logo">
                <img src="{{ asset('images/logo.svg') }}" alt="PT ISEKI INDONESIA">
            </a>
            <ul class="nav-links md-hide-mobile">
                @if(!Auth::check() && !session('employee_login'))
                    <li>
                        <a href="{{ route('replacements.read') }}" class="btn btn-primary">
                            <i class="material-symbols-rounded">person</i>
                            Pengganti
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('show.login') }}" class="btn btn-primary">
                            <i class="material-symbols-rounded">login</i>
                            Masuk
                        </a>
                    </li>
                @endif

                @if(session()->has('employee_login') && session('employee_login'))
                    <li class="{{ request()->is('/') || request()->is('reporting') || request()->is('reporting/*') || request()->is('employee/reporting') ? 'active' : '' }}">
                        <a href="{{ route('employee.reporting') }}">Absensi</a>
                    </li>
                    <li class="{{ request()->is('employee/lembur') ? 'active' : '' }}">
                        <a href="{{ route('employee.lemburs.index') }}">Lembur</a>
                    </li>
                    <li class="user-menu">
                        <button onclick="toggleDropdown()" class="btn btn-secondary">
                            <i class="material-symbols-rounded">account_circle</i>
                            {{ session('employee_user')->name }}
                            <i class="material-symbols-rounded">arrow_drop_down</i>
                        </button>
                        <div id="userDropdown" class="dropdown hidden">
                            <p class="text-sm">Ingin Keluar?</p>
                            <hr>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-danger w-full">
                                    <i class="material-symbols-rounded">logout</i>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </li>
                @elseif(Auth::check())
                    @php
                        $role = Auth::user()->type;
                    @endphp
                    <li class="{{ request()->is('/') || request()->is('reporting') || request()->is('reporting/*') ? 'active' : '' }}">
                        <a href="{{ url('/') }}">Absensi</a>
                    </li>
                    <li class="{{ request()->is('lemburs') || request()->is('lemburs/*') || request()->is('lembur') ? 'active' : '' }}">
                        <a href="{{ route('lemburs.index') }}">Lembur</a>
                    </li>
                    @if(in_array($role, ['admin', 'super']))
                    <li class="{{ request()->is('employees') || request()->is('employees/*') ? 'active' : '' }}">
                        <a href="{{ url('/employees') }}">Pegawai</a>
                    </li>
                    @endif
                    @if($role === 'super')
                    <li class="{{ request()->is('users') || request()->is('users/*') ? 'active' : '' }}">
                        <a href="{{ url('/users') }}">User</a>
                    </li>
                    <li class="{{ request()->is('dates') || request()->is('dates/*') ? 'active' : '' }}">
                        <a href="{{ url('/dates') }}">Tanggal</a>
                    </li>
                    @endif
                    <li class="user-menu">
                        <button onclick="toggleDropdown()" class="btn btn-secondary">
                            <i class="material-symbols-rounded">account_circle</i>
                            {{ Auth::user()->name }}
                            <i class="material-symbols-rounded">arrow_drop_down</i>
                        </button>
                        <div id="userDropdown" class="dropdown hidden">
                            <p class="text-sm">Ingin Keluar?</p>
                            <hr>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-danger w-full">
                                    <i class="material-symbols-rounded">logout</i>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </li>
                @endif
            </ul>
        </div>

        <button id="drawer-menu" class="md-hidden md-hide-desktop" style="background:none;border:none;padding:0.25rem;cursor:pointer;display:none;">
            <i class="material-symbols-rounded" style="font-size:1.75rem;color:var(--primary);">menu</i>
        </button>
    </nav>

    <!-- Drawer overlay backdrop -->
    <div id="drawer-overlay" style="display:none;position:fixed;inset:0;z-index:3000;background:rgba(0,0,0,0.4);backdrop-filter:blur(4px);"></div>

    <!-- Drawer -->
    <div id="drawer">
        <div class="flex justify-between items-center">
            <a href="{{ url('/') }}">
                <img src="{{ asset('images/logo.svg') }}" alt="PT ISEKI INDONESIA" style="height:32px;">
            </a>
            <button id="close-drawer" style="background:none;border:none;cursor:pointer;padding:0.25rem;">
                <i class="material-symbols-rounded" style="font-size:1.5rem;color:var(--text-secondary);">close</i>
            </button>
        </div>
        <hr style="border:none;border-top:1px solid var(--border);">
        <nav>
            <ul class="nav-links">
                @if(!Auth::check() && !session('employee_login'))
                    <li>
                        <a href="{{ route('replacements.read') }}" class="btn btn-primary w-full">
                            <i class="material-symbols-rounded">person</i>
                            Pengganti
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('show.login') }}" class="btn btn-primary w-full">
                            <i class="material-symbols-rounded">login</i>
                            Masuk
                        </a>
                    </li>
                @endif

                @if(session()->has('employee_login') && session('employee_login'))
                    <li class="{{ request()->is('/') || request()->is('reporting') || request()->is('reporting/*') || request()->is('employee/reporting') ? 'active' : '' }}">
                        <a href="{{ route('employee.reporting') }}">Absensi</a>
                    </li>
                    <li class="{{ request()->is('employee/lembur') ? 'active' : '' }}">
                        <a href="{{ route('employee.lemburs.index') }}">Lembur</a>
                    </li>
                    <li class="user-info">
                        <div style="font-size:0.8125rem;font-weight:600;color:var(--text-primary);margin-bottom:0.25rem;">{{ session('employee_user')->name }}</div>
                        <form action="{{ route('logout') }}" method="POST" style="width:100%;">
                            @csrf
                            <button type="submit" class="btn btn-danger w-full" style="font-size:0.8125rem;">
                                <i class="material-symbols-rounded" style="font-size:1.125rem;">logout</i>
                                Logout
                            </button>
                        </form>
                    </li>
                @elseif(Auth::check())
                    @php $role = Auth::user()->type; @endphp
                    <li class="{{ request()->is('/') || request()->is('reporting') || request()->is('reporting/*') ? 'active' : '' }}">
                        <a href="{{ url('/') }}">Absensi</a>
                    </li>
                    <li class="{{ request()->is('lemburs') || request()->is('lemburs/*') || request()->is('lembur') ? 'active' : '' }}">
                        <a href="{{ route('lemburs.index') }}">Lembur</a>
                    </li>
                    @if(in_array($role, ['admin', 'super']))
                    <li class="{{ request()->is('employees') || request()->is('employees/*') ? 'active' : '' }}">
                        <a href="{{ url('/employees') }}">Pegawai</a>
                    </li>
                    @endif
                    @if($role === 'super')
                    <li class="{{ request()->is('users') || request()->is('users/*') ? 'active' : '' }}">
                        <a href="{{ url('/users') }}">User</a>
                    </li>
                    <li class="{{ request()->is('dates') || request()->is('dates/*') ? 'active' : '' }}">
                        <a href="{{ url('/dates') }}">Tanggal</a>
                    </li>
                    @endif
                    <li class="user-info">
                        <div style="font-size:0.8125rem;font-weight:600;color:var(--text-primary);margin-bottom:0.25rem;">{{ Auth::user()->name }}</div>
                        <form action="{{ route('logout') }}" method="POST" style="width:100%;">
                            @csrf
                            <button type="submit" class="btn btn-danger w-full" style="font-size:0.8125rem;">
                                <i class="material-symbols-rounded" style="font-size:1.125rem;">logout</i>
                                Logout
                            </button>
                        </form>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
</header>

<style>
    /* Override nav-links in drawer to be column */
    #drawer .nav-links {
        display: flex;
        flex-direction: column;
        width: 100%;
    }
    #drawer .nav-links li {
        width: 100%;
    }
    #drawer .nav-links li a {
        width: 100%;
        padding: 0.625rem 0.75rem;
        border-radius: 6px;
    }
    #drawer .nav-links li.user-info {
        display: flex;
        flex-direction: column;
        padding: 0.5rem 0.75rem;
    }
    @media (max-width: 768px) {
        .md-hide-mobile { display: none !important; }
        #drawer-menu { display: flex !important; }
        #drawer { display: flex; }
    }
    @media (min-width: 769px) {
        .md-hide-desktop { display: none !important; }
    }
</style>

<script>
    const drawer = document.getElementById('drawer');
    const drawerOverlay = document.getElementById('drawer-overlay');
    const openBtn = document.getElementById('drawer-menu');
    const closeBtn = document.getElementById('close-drawer');

    function openDrawer() {
        drawer.classList.add('show');
        drawerOverlay.style.display = 'block';
    }
    function closeDrawer() {
        drawer.classList.remove('show');
        drawerOverlay.style.display = 'none';
    }

    openBtn.addEventListener('click', openDrawer);
    closeBtn.addEventListener('click', closeDrawer);
    drawerOverlay.addEventListener('click', closeDrawer);

    function toggleDropdown() {
        document.querySelectorAll('.user-menu .dropdown').forEach(el => {
            el.classList.toggle('hidden');
        });
    }

    document.addEventListener('click', function(e) {
        document.querySelectorAll('.user-menu').forEach(menu => {
            const btn = menu.querySelector('button');
            const dd = menu.querySelector('.dropdown');
            if (!btn.contains(e.target) && !dd.contains(e.target)) {
                dd.classList.add('hidden');
            }
        });
    });
</script>
