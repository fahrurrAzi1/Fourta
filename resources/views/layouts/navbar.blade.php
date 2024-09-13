<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- link bagian kanan -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        {{-- <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ route('home') }}" class="nav-link">Home</a>
        </li> --}}
    </ul>

    <ul class="navbar-nav ml-auto">
        <!-- navbar kanan -->
        <li class="nav-item dropdown">
            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                {{ Auth::user()->name }}
            </a>

            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                @csrf

                @if (Auth::user()->role == 'guru')
                    <a class="dropdown-item" href="#">
                        <i class="nav-icon fas fa-user ml-2"></i>&nbsp;&nbsp;&nbsp;
                        {{ __('Profile') }}
                    </a>
                    <a class="dropdown-item" href="{{ route('logout') }}"
                    onclick="event.preventDefault();
                                    document.getElementById('logout-form').submit();">

                        <i class="nav-icon fas fa-door-open ml-2"></i>&nbsp;&nbsp;
                        {{ __('Logout') }}
                    </a>

                    <form id="logout-form" action="{{ route('guru.logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>

                @elseif (Auth::user()->role == 'siswa')
                    <a class="dropdown-item" href="#">
                        <i class="nav-icon fas fa-user ml-2"></i>&nbsp;&nbsp;&nbsp;
                        {{ __('Profile') }}
                    </a>
                    <a class="dropdown-item" href="{{ route('logout') }}"
                    onclick="event.preventDefault();
                                    document.getElementById('logout-form').submit();">

                        <i class="nav-icon fas fa-door-open ml-2"></i>&nbsp;&nbsp;
                        {{ __('Logout') }}
                    </a>

                    <form id="logout-form" action="{{ route('siswa.logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                @endif
            </div>
        </li>
    </ul>
</nav>
