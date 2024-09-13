@extends('layouts.app')

    @section('header')

    <title>Siswa Dashboard</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    @endsection

    @section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="jumbotron mt-4">
                    <h1>Welcome, {{ Auth::user()->name }}</h1>
                    @if($kelas && $kelas->guru)
                        <p class="lead">Selamat Datang Siswa/Siswi Sekolah {{ $kelas->nama_sekolah }} di Kelas {{ $kelas->id_kelas }}, <br> Nama Pengajar : {{ $kelas->guru->name }}.</p>
                    @else
                        <p class="lead">Anda belum terdaftar di kelas mana pun.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script> --}}
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    @endsection
