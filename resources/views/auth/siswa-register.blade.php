<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Siswa Register</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container-fluid">
        <div class="row no-gutters vh-100">
            <div class="col-lg-6 d-none d-lg-block bg-image"
                style="background-image: url('{{ asset('images/bgregistersiswa.svg') }}'); background-size: cover; background-position: center;">
            </div>

            <div class="col-lg-6 d-flex align-items-center justify-content-center">
                <div class="container">
                    <h2 class="text-center mb-4">Siswa Register</h2>
                    <form method="POST" action="{{ route('siswa.register') }}">
                        @csrf
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" name="name" class="form-control" placeholder="Name" required>
                        </div>

                        <div class="form-group">
                            <label for="nis">NIS</label>
                            <input type="number" name="nis" class="form-control" placeholder="NIS" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="Email" required>
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Password" required>
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm Password" required>
                        </div>

                        <div class="form-group">
                            <label for="id_kelas">Kelas</label>
                            <select name="id_kelas" class="form-control" required>
                                <option value="">Pilih Kelas</option>
                                @foreach($kelas as $kelasItem)
                                    <option value="{{ $kelasItem->id }}">{{ $kelasItem->id_kelas }} - {{ $kelasItem->nama_sekolah }} - {{ $kelasItem->guru->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">Register</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>

</html>
