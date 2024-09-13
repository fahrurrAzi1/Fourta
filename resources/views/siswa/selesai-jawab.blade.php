@extends('layouts.jawab')

    @section('header')

    <title>Selesai menjawab</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    @endsection

    @section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="jumbotron mt-4">
                    <h1>Selamat {{ Auth::user()->name }}, dari Kelas {{ Auth::user()->kelass->id_kelas }} anda telah berhasil menyelesaikan assasemen dengan jenis soal: {{ ucfirst(session('jenis')) }}.</h1>
                    <h3>silahkan memeriksa hasil di panel hasil jawaban pada halaman kalian.</h3>
                    <h4 class="text-bold">(catatan: hasil akan muncul setelah guru memberi penilaian terhadap pekerjaan kalian)</h4>
                    <center>
                        <button class="btn btn-primary" onclick="confirmExit1()">Lanjutkan</button>
                    </center>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script> --}}
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        function confirmExit1() {
            Swal.fire({
                title: 'Apakah anda yakin ?',
                text: 'Anda akan melanjutkan ke halaman pilihan soal.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Iya',
                cancelButtonText: 'Tidak',
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('siswa.jawab-soal') }}";
                }
            });
        }
    </script>

    @endsection
