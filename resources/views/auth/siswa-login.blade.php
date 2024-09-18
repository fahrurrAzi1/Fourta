<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Siswa Login</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container-fluid">
        <div class="row no-gutters vh-100">
            <div class="col-lg-6 d-none d-lg-block bg-image"
                style="background-image: url('{{ asset('images/bgloginsiswa.svg') }}'); background-size: cover; background-position: center;">
            </div>

            <div class="col-lg-6 d-flex align-items-center justify-content-center">
                <div class="container">

                    <div class="text-center mb-4 d-lg-none">
                        <img src="{{ asset('images/bgloginsiswa.svg') }}" alt="Login Image" class="img-fluid" style="max-width: 300px;">
                    </div>

                    <h2 class="text-center mb-4">Siswa Login</h2>

                    <form method="POST" action="{{ route('siswa.login') }}">
                        @csrf
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password" required>
                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">Login</button>
                    </form>

                    <div class="text-center mt-3">
                        <p>Login sebagai <a href="{{ route('guru.login') }}">Guru</a></p>
                        <p>Belum punya akun? <a href="{{ route('siswa.register') }}">Daftar sebagai Siswa</a></p>
                    </div>

                </div>
            </div>
        </div>
    </div>

    @if ($errors->any())
    <div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="errorModalLabel">Login Error</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <strong>Oops!</strong> There was a problem with your login attempt.
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function() {
            @if ($errors->any())
                $('#errorModal').modal('show');
            @endif
        });
    </script>

</body>

</html>
