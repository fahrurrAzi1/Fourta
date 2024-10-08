<!DOCTYPE html>
<html>
<head>
    <title>Login and Register</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body, html {
            height: 100%;
        }
        .center-container {
            height: 100vh;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
<div class="container-fluid center-container d-flex flex-column justify-content-center">
    <h2 class="font-weight-bold">Four Tier Assessment</h2>
    <img src="{{ asset('images/bgwelcome.svg') }}" alt="Header Image" class="img-fluid w-100 rounded">
    <div class="row justify-content-center mt-4">
        <div class="col-md-6 text-center">
            <div class="btn-group">
                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Login sebagai
                </button>
                {{-- dropdown menu --}}
                <div class="dropdown-menu">
                    {{-- <a class="dropdown-item" href="{{ route('admin.login') }}">Admin</a> --}}
                    <a class="dropdown-item" href="{{ route('guru.login') }}">Guru</a>
                    <a class="dropdown-item" href="{{ route('siswa.login') }}">Siswa</a>
                </div>
            </div>
            <div class="btn-group ml-2">
                <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Register sebagai
                </button>
                {{-- dropdown menu --}}
                <div class="dropdown-menu">
                    {{-- <a class="dropdown-item" href="{{ route('admin.register') }}">Admin</a> --}}
                    <a class="dropdown-item" href="{{ route('guru.register') }}">Guru</a>
                    <a class="dropdown-item" href="{{ route('siswa.register') }}">Siswa</a>
                </div>
            </div>

            <a href="https://drive.google.com/drive/folders/1Bo3nrDj-Kv_FyVLJMDQASC9C4iEBiv3Q?usp=drive_link" target="_blank" class="btn btn-info ml-2">
                <i class="fas fa-info-circle"></i> Panduan Penggunaan
            </a>

        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
