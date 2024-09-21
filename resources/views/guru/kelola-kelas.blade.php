@extends('layouts.app')

    @section('header')
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Halaman Guru: Kelola Kelas') }}
        </h2>
    @endsection

    @section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="container mt-3">
                    @if(session('success'))
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Sukses',
                                    text: "{{ session('success') }}",
                                    confirmButtonText: 'OK',
                                });
                            });
                        </script>
                    @endif

                    <div class="row">
                        <div class="col-md-9">
                            <form action="#" method="post">
                                @csrf
                                <div class="form-group">
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="mt-2">
                        <h4>Daftar Kelas</h4>
                        <table class="table table-bordered" id="kelasTable">
                            <thead>
                                <tr>
                                    <th>Kelas</th>
                                    <th>Sekolah</th>
                                    <th>Nama Guru</th>
                                    <th>Jumlah Siswa</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="kelasModal" tabindex="-1" aria-labelledby="kelasModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="kelasModalLabel">Tambah Kelas</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formTambahKelas" action="{{ url('guru/kelas/tambah') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="idKelas">Kelas</label>
                            <input type="text" class="form-control" id="idKelas" name="id_kelas" required>
                        </div>
                        <div class="form-group">
                            <label for="nama_sekolah">Sekolah</label>
                            <input type="text" class="form-control" id="nama_sekolah" name="nama_sekolah" required>
                        </div>
                        <button type="button" class="btn btn-primary" id="saveKelasBtn">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>    

    <!-- Modal for Viewing Students -->
    <div class="modal fade" id="studentsModal" tabindex="-1" aria-labelledby="studentsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="studentsModalLabel">Daftar Siswa</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered" id="studentsTable">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">Nama</th>
                                <th class="text-center">Kelas</th>
                                <th class="text-center">Sekolah</th>
                                <th class="text-center">NIS</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <!-- Skrip jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <!-- Skrip untuk Data Table -->
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>


    <!-- Skrip Popper JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>

    <!-- Skrip Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#kelasTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('guru.kelola-kelas') }}",
                columns: [
                    {data: 'id_kelas', name: 'id_kelas'},
                    {data: 'nama_sekolah', name: 'nama_sekolah'},
                    {data: 'guru.name', name: 'guru.name'},
                    {data: 'jumlah_siswa', name: 'jumlah_siswa', title: 'Jumlah Siswa'},
                    {data: 'aksi', name: 'aksi'},
                ],
                initComplete: function () {
                    
                    $('div.dataTables_filter input').addClass('form-control');
                    $('div.dataTables_filter').addClass('d-flex justify-content-end align-items-center');
                    
                    $('div.dataTables_filter').append(`
                        <button type="button" class="btn btn-primary ml-3 mt-3 font-weight-bold" data-toggle="modal" data-target="#kelasModal" id="tambahKelasBtn">
                            Tambah Kelas
                        </button>
                    `);

                }
            });

            $(document).on('click', '.view-students', function() {
                var kelasId = $(this).data('id');

                $.ajax({
                    url: '/guru/kelas/siswa/' + kelasId,
                    type: 'GET',
                    success: function(response) {
                        var tableBody = $('#studentsTable tbody');
                        tableBody.empty();  

                        let index = 1; 

                        response.siswas.forEach(function(siswas) {
                            tableBody.append(`
                                <tr>
                                    <td class="text-center">${index++}</td>
                                    <td class="text-center">${siswas.name}</td>
                                    <td class="text-center">${siswas.kelass.id_kelas}</td>
                                    <td class="text-center">${siswas.nama_sekolah}</td>
                                    <td class="text-center">${siswas.nis}</td>
                                </tr>
                            `);
                        });

                        $('#studentsModal').modal('show');
                    },
                    error: function() {
                        alert('Gagal mengambil data siswa');
                    }
                });
            });

            $(document).on('click', '#tambahKelasBtn', function() {
                $('#formTambahKelas').trigger('reset'); 
                $('#formTambahKelas').attr('action', "{{ url('guru/kelas/tambah') }}"); 
                $('#kelasModalLabel').text('Tambah Kelas'); 
            });

            $(document).on('click', '.edit-class', function() {
                var id = $(this).data('id');
                var kelas = $(this).data('kelas');
                var sekolah = $(this).data('sekolah');

                $('#idKelas').val(kelas);
                $('#nama_sekolah').val(sekolah);
                $('#formTambahKelas').attr('action', '/guru/kelas/edit/' + id); 

                $('#kelasModalLabel').text('Edit Kelas');

                $('#kelasModal').modal('show');
            });

            $(document).on('click', '.delete-class', function() {
                var classId = $(this).data('id');
                var form = $('#delete-form-' + classId);

                Swal.fire({
                    title: 'Anda yakin?',
                    text: "Kelas ini akan dihapus!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            $('#saveKelasBtn').click(function() {
                Swal.fire({
                    title: 'Simpan Kelas?',
                    text: "Pastikan data yang Anda masukkan sudah benar!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, simpan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#formTambahKelas').submit();
                    }
                });
            });
        });
    </script>

    @endsection
