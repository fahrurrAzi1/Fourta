@extends('layouts.app')

    @section('header')
        <title>Pilih Jenis Pertanyaan</title>
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @endsection

    @section('content')

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                @if(!session('jenis') || !session('kelas_id'))
                    <h2 class="font-weight-bold">Pilih Jenis Soal</h2>
                    <div class="row">
                        <div class="col-md-8 d-flex justify-content-center">
                            <button type="button" class="btn btn-success mb-2 font-weight-bold d-flex flex-column align-items-center justify-content-center" onclick="confirmOption('literasi')" style="width: 400px; height: 100px;">
                                <i class="fas fa-book fa-2x mb-1"></i>
                                Literasi
                            </button>
                            <p class="font-weight-bold ml-2 mt-4">Atau</p>
                            <button type="button" class="btn btn-warning mb-2 ml-2 font-weight-bold d-flex flex-column align-items-center justify-content-center" onclick="confirmOption('numerasi')" style="width: 400px; height: 100px;">
                                <i class="fas fa-calculator fa-2x mb-1"></i>
                                Numerasi
                            </button>
                        </div>
                    </div>
                @else
                <script>
                    window.location.href = "{{ route('siswa.isi-soal') }}";
                </script>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal untuk memilih kelas -->
    <div class="modal fade" id="kelasModal" tabindex="-1" aria-labelledby="kelasModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="kelasModalLabel">Pilih Kelas</h5>
                    <button type="button" class="close" onclick="closeModal()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <ul class="list-group">
                        @foreach($kelass as $kelas)
                            <btn class="btn btn-primary mb-2" onclick="selectKelas({{ $kelas->id }})">{{ $kelas->id_kelas }} - {{ $kelas->nama_sekolah }}</btn>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        let selectedJenis;

        function closeModal() {
            $('#kelasModal').modal('hide');
        }

        function confirmOption(jenis) {
            selectedJenis = jenis;
            Swal.fire({
                title: 'Apakah anda yakin ?',
                text: 'Anda ingin memilih jenis soal ini.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Iya',
                cancelButtonText: 'Tidak',
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tampilkan modal untuk memilih kelas
                    $('#kelasModal').modal('show');
                }
            });
        }

        function selectKelas(kelas_id) {
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah anda yakin memilih kelas ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Iya',
                cancelButtonText: 'Tidak',
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect dengan parameter jenis soal dan kelas_id
                    window.location.href = "{{ route('siswa.filter') }}" + "?jenis=" + selectedJenis + "&kelas_id=" + kelas_id;
                }
            });
        }
    </script>

    @endsection

    {{-- alternatif kode --}}
    {{-- <div class="btn-group mb-3" role="group">
        <a href="#" class="btn btn-outline-primary" onclick="confirmOption('literasi')">Literasi</a>
        <a href="#" class="btn btn-outline-primary" onclick="confirmOption('numerasi')">Numerasi</a>
    </div> --}}