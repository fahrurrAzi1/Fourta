<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- logo media -->
    <a href="#" class="brand-link ml-2 text-decoration-none">
        <span class="brand-text font-weight-bold">{{ config('app.name', 'Laravel') }}</span>
    </a>

    <!-- sidebar -->
    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex flex-column">
        @csrf

            @if (Auth::user()->role == 'admin')
                <div class="info">
                    <a href="#" class="d-block text-decoration-none">Nama:&nbsp;
                        {{ Auth::user()->name }}</a>
                </div>
            @elseif (Auth::user()->role == 'guru')
                <div class="info">
                    <a href="#" class="d-block text-decoration-none">Nama:&nbsp;
                        {{ Auth::user()->name }}</a>
                </div>
                <div class="info">
                    <a href="#" class="d-block text-decoration-none">NIP:&nbsp;
                        {{ Auth::user()->nip }}</a>
                </div>  
            @elseif (Auth::user()->role == 'siswa')
                
                <div class="info">
                    <a href="#" class="d-block font-weight-bold text-decoration-none">
                            {{ Auth::user()->nama_sekolah }}</a>
                </div>  
                <div class="info">
                    <a href="#" class="d-block text-decoration-none">Nama:&nbsp;
                        {{ Auth::user()->name }}</a>
                    <a href="#" class="d-block text-decoration-none">NIS:&nbsp;
                            {{ Auth::user()->nis }}</a>
                    <a href="#" class="d-block text-decoration-none">Kelas:&nbsp;
                            {{ Auth::user()->kelass->id_kelas }}</a>
                </div>  
            @endif
        </div>

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            @csrf

                @if (Auth::user()->role == 'admin')
                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard') }}" class="nav-link">
                            <i class="nav-icon fas fa-chalkboard-teacher"></i>
                            <p>Admin Dashboard</p>
                        </a>
                    </li>
                @elseif (Auth::user()->role == 'guru')
                    <li class="nav-item">
                        <a href="{{ route('guru.dashboard') }}" class="nav-link">
                            <i class="nav-icon fas fa-chalkboard-teacher"></i>
                            <p>Guru Dashboard</p>
                        </a>
                    </li>
                @elseif (Auth::user()->role == 'siswa')
                    <li class="nav-item">
                        <a href="{{ route('siswa.dashboard') }}" class="nav-link">
                            <i class="nav-icon fas fa-user-graduate"></i>
                            <p>Siswa Dashboard</p>
                        </a>
                    </li>
                @endif
                <li class="nav-item">
                    @if (Auth::user()->role == 'admin')
                        <li class="nav-item">
                            <a href="{{ route('admin.kelola-kelas-admin') }}" class="nav-link">
                                <i class="nav-icon fas fas fa-school"></i>
                                <p>Kelola Kelas</p>
                            </a>
                        </li>
                    @elseif (Auth::user()->role == 'guru')
                        <li class="nav-item">
                            <a href="{{ route('guru.kelola-kelas') }}" class="nav-link">
                                <i class="nav-icon fas fas fa-school"></i>
                                <p>Kelola Kelas</p>
                            </a>
                        </li>
                    @elseif (Auth::user()->role == 'siswa')
                        <li class="nav-item">
                            <a href="{{ route('siswa.jawab-soal') }}" class="nav-link">
                                <i class="nav-icon fas fas fa-file-alt"></i>
                                <p>Jawab Pertanyaan</p>
                            </a>
                        </li>
                    @endif
                </li>
                <li class="nav-item">
                    @if (Auth::user()->role == 'admin')
                        <li class="nav-item">
                            <a href="{{ route('admin.input-soal-admin') }}" class="nav-link">
                                <i class="nav-icon fas fas fa-file-alt"></i>
                                <p>Input Soal</p>
                            </a>
                        </li>
                    @elseif (Auth::user()->role == 'guru')
                        <li class="nav-item">
                            <a href="{{ route('guru.input-soal') }}" class="nav-link">
                                <i class="nav-icon fas fas fa-file-alt"></i>
                                <p>Input Soal</p>
                            </a>
                        </li>
                    @elseif (Auth::user()->role == 'siswa')
                        <li class="nav-item">
                            <a href="{{ route('siswa.hasil-jawaban') }}" class="nav-link">
                                <i class="nav-icon fas fa-clipboard-check"></i>
                                <p>Hasil Jawaban</p>
                            </a>
                        </li>
                    @endif
                </li>
                <li class="nav-item">
                    @if (Auth::user()->role == 'admin')
                       <li class="nav-item">
                            <a href="{{ route('admin.kelola-jawaban-admin') }}" class="nav-link">
                                <i class="nav-icon fas fa-clipboard-check"></i>
                                <p>Kelola Jawaban</p>
                            </a>
                        </li>
                    @elseif (Auth::user()->role == 'guru')
                        <li class="nav-item">
                            <a href="{{ route('guru.kelola-jawaban') }}" class="nav-link">
                                <i class="nav-icon fas fa-clipboard-check"></i>
                                <p>Kelola Jawaban</p>
                            </a>
                        </li>
                    @elseif (Auth::user()->role == 'siswa')
                        <!-- role siswa dikosongkan -->
                    @endif
                </li>
                <li class="nav-item">
                    @if (Auth::user()->role == 'admin')
                        <li class="nav-item">
                            <a href="{{ route('admin.presentase-hasil-admin') }}" class="nav-link">
                                <i class="nav-icon fas fa-chart-simple"></i>
                                <p>Presentase Hasil Siswa</p>
                            </a>
                        </li>
                    @elseif (Auth::user()->role == 'guru')
                        <li class="nav-item">
                            <a href="{{ route('guru.presentase-hasil') }}" class="nav-link">
                                <i class="nav-icon fas fa-chart-simple"></i>
                                <p>Presentase Hasil Siswa</p>
                            </a>
                        </li>
                    @elseif (Auth::user()->role == 'siswa')
                        <!-- tombol kosong -->
                    @endif
                </li>
                <li class="nav-item">
                    @if (Auth::user()->role == 'admin')
                        <a href="{{ route('logout') }}" class="nav-link" onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();">
                            <i class="nav-icon fas fa-door-open"></i>
                            <p>Logout</p>
                        </a>
                        <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    @elseif (Auth::user()->role == 'guru')
                        <a href="{{ route('logout') }}" class="nav-link" onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();">
                            <i class="nav-icon fas fa-door-open"></i>
                            <p>Logout</p>
                        </a>
                        <form id="logout-form" action="{{ route('guru.logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    @elseif (Auth::user()->role == 'siswa')
                        <a href="{{ route('logout') }}" class="nav-link" onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();">
                            <i class="nav-icon fas fa-door-open"></i>
                            <p>Logout</p>
                        </a>
                        <form id="logout-form" action="{{ route('siswa.logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    @endif
                </li>
            </ul>
        </nav>
    </div>
</aside>

{{-- alternatif kode --}}
{{-- <div class="image">
        <img src="#" class="img-circle elevation-2" alt="-">
</div> --}}

{{-- <img src="{{ asset('images/AdminLTELogo.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8"> --}}