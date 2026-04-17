<header class="sticky top-0 z-50 bg-white shadow">
    <nav class="navbar sticky">
        <div class="container flex items-center justify-between p-4">
            <!-- Drawer Trigger + Logo -->
            <div class="flex justify-between items-center gap-4">
                <a href="{{ url('/') }}" class="logo">
                    <img src="{{ asset('images/logo.svg') }}" alt="PT ISEKI INDONESIA" class="h-8">
                </a>
            </div>
            <button id="drawer-menu" class="md-hidden items-center md:hidden">
                <i class="material-symbols-rounded btn-primary">
                    menu
                </i>
            </button>

            <!-- Normal Desktop Nav -->
            <ul class="nav-links items-center">
                @if(!Auth::check() && !(session()->has('employee_login') && session('employee_login')))
                    <li class="user-menu">
                        <a href="{{ route('replacements.read') }}" class="btn btn-primary">
                            <i class="material-symbols-rounded">person</i>
                            Pengganti
                        </a>
                    </li>

                    <li class="user-menu">
                        <a href="{{ route('show.login') }}" class="btn btn-primary">
                            <i class="material-symbols-rounded">person</i>
                            Masuk
                        </a>
                    </li>
                @endif


                {{-- Cek apakah user login sebagai employee --}}
                @if(session()->has('employee_login') && session('employee_login'))
                    <li class="{{ request()->is('/') || request()->is('reporting') || request()->is('reporting/*') ? 'active' : '' }}">
                        <a href="{{ route('employee.reporting') }}">Report</a>
                    </li>
                    <li class="user-menu">
                        <button onclick="toggleDropdown()" class="btn btn-secondary user-button">
                            Hai, {{ session('employee_user')->name }}
                            <i class="material-symbols-rounded">
                                arrow_drop_down
                            </i>
                        </button>

                        <div id="userDropdown" class="user-dropdown dropdown hidden">
                            <p class="text-sm">Ingin Keluar?</p>
                            <hr>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-danger">
                                    <i class="material-symbols-rounded">
                                        mode_off_on
                                    </i>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </li>
                @elseif(Auth::check())
                    {{-- Navbar untuk user biasa --}}
                    @userType('leader')
                        <li class="{{ request()->is('/') || request()->is('reporting') || request()->is('reporting/*') ? 'active' : '' }}">
                            <a href="{{ url('/') }}">Report</a>
                        </li>
                        <li class="{{ request()->is('lemburs') || request()->is('lemburs/*') ? 'active' : '' }}">
                            <a href="{{ route('lemburs.index') }}">Lembur</a>
                        </li>
                        <li class="user-menu">
                            <button onclick="toggleDropdown()" class="btn btn-secondary user-button">
                                Hai, {{ Auth::user()->name }}
                                <i class="material-symbols-rounded">
                                    arrow_drop_down
                                </i>
                            </button>

                            <div id="userDropdown" class="user-dropdown dropdown hidden">
                                <p class="text-sm">Ingin Keluar?</p>
                                <hr>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-danger">
                                        <i class="material-symbols-rounded">
                                            mode_off_on
                                        </i>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </li>
                    @enduserType

                    @userType('admin')
                        <li class="{{ request()->is('/') || request()->is('reporting') || request()->is('reporting/*') ? 'active' : '' }}">
                            <a href="{{ url('/') }}">Report</a>
                        </li>
                        <li class="{{ request()->is('/') || request()->is('lembur') || request()->is('reporting/*') ? 'active' : '' }}">
                            <a href="{{ url('lembur') }}">Lembur</a>
                        </li>
                        <li class="{{ request()->is('employees') || request()->is('employees/*') ? 'active' : '' }}">
                            <a href="{{ url('/employees') }}">Pegawai</a>
                        </li>
                        <li class="user-menu">
                            <button onclick="toggleDropdown()" class="btn btn-secondary user-button">
                                Hai, {{ Auth::user()->name }}
                                <i class="material-symbols-rounded">
                                    arrow_drop_down
                                </i>
                            </button>

                            <div id="userDropdown" class="user-dropdown dropdown hidden">
                                <p class="text-sm">Ingin Keluar?</p>
                                <hr>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-danger">
                                        <i class="material-symbols-rounded">
                                            mode_off_on
                                        </i>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </li>
                    @enduserType

                    @userType('super')
                        <li class="{{ request()->is('/') || request()->is('reporting') || request()->is('reporting/*') ? 'active' : '' }}">
                            <a href="{{ url('/') }}">Report</a>
                        </li>
                        <li class="{{ request()->is('/') || request()->is('lembur') || request()->is('reporting/*') ? 'active' : '' }}">
                            <a href="{{ url('lembur') }}">Lembur</a>
                        </li>
                        <li class="{{ request()->is('employees') || request()->is('employees/*') ? 'active' : '' }}">
                            <a href="{{ url('/employees') }}">Pegawai</a>
                        </li>
                        <li class="{{ request()->is('users') || request()->is('users/*') ? 'active' : '' }}">
                            <a href="{{ url('/users') }}">User</a>
                        </li>
                        <li class="{{ request()->is('dates') || request()->is('dates/*') ? 'active' : '' }}">
                            <a href="{{ url('/dates') }}">Tanggal</a>
                        </li>
                        <li class="user-menu">
                            <button onclick="toggleDropdown()" class="btn btn-secondary user-button">
                                Hai, {{ Auth::user()->name }}
                                <i class="material-symbols-rounded">
                                    arrow_drop_down
                                </i>
                            </button>

                            <div id="userDropdown" class="user-dropdown dropdown hidden">
                                <p class="text-sm">Ingin Keluar?</p>
                                <hr>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-danger">
                                        <i class="material-symbols-rounded">
                                            mode_off_on
                                        </i>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </li>
                    @enduserType
                @endif
            </ul>
        </div>
    </nav>

    <!-- Drawer Nav -->
    <div id="drawer"
        class="fixed z-50 top-0 left-0 h-full w-64 bg-white shadow-lg transform -translate-x-full transition-transform duration-300 ease-in-out md:hidden flex flex-col p-6 space-y-6 gap-2">
        <div class="flex justify-between items-center">
            <a href="{{ url('/') }}">
                <img src="{{ asset('images/logo.svg') }}" alt="PT ISEKI INDONESIA" class="h-8">
            </a>
            <button id="close-drawer">
                <i class="material-symbols-rounded">
                    close
                </i>
            </button>
        </div>

        <nav class="flex flex-col">
            <ul class="nav-links">
                @if(!Auth::check() && !(session()->has('employee_login') && session('employee_login')))
                    <li class="user-menu">
                        <a href="{{ route('replacements.read') }}" class="btn btn-primary">
                            <i class="material-symbols-rounded">
                                person
                            </i>
                            Pengganti
                        </a>
                    </li>
                    <li class="user-menu">
                        <a href="{{ route('show.login') }}" class="btn btn-primary">
                            <i class="material-symbols-rounded">
                                person
                            </i>
                            Masuk
                        </a>
                    </li>
                @endif

                {{-- Cek apakah user login sebagai employee --}}
                @if(session()->has('employee_login') && session('employee_login'))
                    <li class="{{ request()->is('/') || request()->is('reporting') || request()->is('reporting/*') ? 'active' : '' }}">
                        <a href="{{ route('employee.reporting') }}">Report</a>
                    </li>
                    <li class="user-menu">
                        <button onclick="toggleDropdown()" class="btn btn-secondary user-button">
                            Hai, {{ session('employee_user')->name }}
                            <i class="material-symbols-rounded">
                                arrow_drop_down
                            </i>
                        </button>

                        <div id="userDropdown" class="user-dropdown dropdown hidden">
                            <p class="text-sm">Ingin Keluar?</p>
                            <hr>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-danger">
                                    <i class="material-symbols-rounded">
                                        mode_off_on
                                    </i>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </li>
                @elseif(Auth::check())
                    {{-- Drawer navbar untuk user biasa --}}
                    @userType('leader')
                        <li class="{{ request()->is('/') || request()->is('reporting') || request()->is('reporting/*') ? 'active' : '' }}">
                            <a href="{{ url('/') }}">Report</a>
                        </li>
                        <li class="{{ request()->is('lemburs') || request()->is('lemburs/*') ? 'active' : '' }}">
                            <a href="{{ route('lemburs.index') }}">Lembur</a>
                        </li>
                        <li class="user-menu">
                            <button onclick="toggleDropdown()" class="btn btn-secondary user-button">
                                Hai, {{ Auth::user()->name }}
                                <i class="material-symbols-rounded">
                                    arrow_drop_down
                                </i>
                            </button>

                            <div id="userDropdown" class="user-dropdown dropdown hidden">
                                <p class="text-sm">Ingin Keluar?</p>
                                <hr>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-danger">
                                        <i class="material-symbols-rounded">
                                            mode_off_on
                                        </i>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </li>
                    @enduserType

                    @userType('admin')
                        <li class="{{ request()->is('/') || request()->is('reporting') || request()->is('reporting/*') ? 'active' : '' }}">
                            <a href="{{ url('/') }}">Report</a>
                        </li>
                        <li class="{{ request()->is('/') || request()->is('lembur') || request()->is('reporting/*') ? 'active' : '' }}">
                            <a href="{{ url('lembur') }}">Lembur</a>
                        </li>
                        <li class="{{ request()->is('employees') || request()->is('employees/*') ? 'active' : '' }}">
                            <a href="{{ url('/employees') }}">Pegawai</a>
                        </li>
                        <li class="user-menu">
                            <button onclick="toggleDropdown()" class="btn btn-secondary user-button">
                                Hai, {{ Auth::user()->name }}
                                <i class="material-symbols-rounded">
                                    arrow_drop_down
                                </i>
                            </button>

                            <div id="userDropdown" class="user-dropdown dropdown hidden">
                                <p class="text-sm">Ingin Keluar?</p>
                                <hr>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-danger">
                                        <i class="material-symbols-rounded">
                                            mode_off_on
                                        </i>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </li>
                    @enduserType

                    @userType('super')
                        <li class="{{ request()->is('/') || request()->is('reporting') || request()->is('reporting/*') ? 'active' : '' }}">
                            <a href="{{ url('/') }}">Report</a>
                        </li>
                        <li class="{{ request()->is('/') || request()->is('lembur') || request()->is('reporting/*') ? 'active' : '' }}">
                            <a href="{{ url('lembur') }}">Lembur</a>
                        </li>
                        <li class="{{ request()->is('employees') || request()->is('employees/*') ? 'active' : '' }}">
                            <a href="{{ url('/employees') }}">Pegawai</a>
                        </li>
                        <li class="{{ request()->is('users') || request()->is('users/*') ? 'active' : '' }}">
                            <a href="{{ url('/users') }}">User</a>
                        </li>
                        <li class="{{ request()->is('dates') || request()->is('dates/*') ? 'active' : '' }}">
                            <a href="{{ url('/dates') }}">Tanggal</a>
                        </li>
                        <li class="user-menu">
                            <button onclick="toggleDropdown()" class="btn btn-secondary user-button">
                                Hai, {{ Auth::user()->name }}
                                <i class="material-symbols-rounded">
                                    arrow_drop_down
                                </i>
                            </button>

                            <div id="userDropdown" class="user-dropdown dropdown hidden">
                                <p class="text-sm">Ingin Keluar?</p>
                                <hr>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-danger">
                                        <i class="material-symbols-rounded">
                                            mode_off_on
                                        </i>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </li>
                    @enduserType
                @endif
            </ul>
        </nav>
    </div>
</header>

<script>
    const drawer = document.getElementById('drawer');
    const openBtn = document.getElementById('drawer-menu');
    const closeBtn = document.getElementById('close-drawer');

    openBtn.addEventListener('click', () => {
        drawer.classList.add('show');
    });

    closeBtn.addEventListener('click', () => {
        drawer.classList.remove('show');
    });

    function toggleDropdown() {
        document.querySelectorAll('.user-dropdown').forEach(el => {
            el.classList.toggle('hidden');
        });
    }

    // Tutup nav kalau klik di luar
    document.addEventListener('click', function(e) {
        const buttons = document.querySelectorAll('.user-button');
        const dropdowns = document.querySelectorAll('.user-dropdown');
        let clickedInside = false;

        buttons.forEach((btn, i) => {
            if (btn.contains(e.target) || dropdowns[i].contains(e.target)) {
                clickedInside = true;
            }
        });

        if (!clickedInside) {
            dropdowns.forEach(d => d.classList.add('hidden'));
        }
    });
</script>