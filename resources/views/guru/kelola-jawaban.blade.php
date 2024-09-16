@extends('layouts.app')
    @section('header')
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Halaman Guru: Kelola Jawaban') }}
        </h2>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @endsection

    @section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 overflow-y-auto">
            <div class="bg-white overflow-auto shadow-sm sm:rounded-lg">
                <div class="container mt-3">
                    <div class="row">
                        <div class="col-md-12">
                            <form action="#" method="post">
                                @csrf
                                <div class="form-group mt-2">
                                    <label for="content">Kelola Jawaban</label>
                                </div>

                                <div class="col-md-8 d-flex justify-content-center">
                                    <button type="button" class="btn btn-success mb-2 font-weight-bold d-flex flex-column align-items-center justify-content-center" id="literasi-button" style="width: 400px; height: 100px;">
                                        <i class="fas fa-book fa-2x mb-1"></i>
                                        Literasi
                                    </button>

                                    <p class="font-weight-bold ml-2 mt-4">Atau</p>

                                    <button type="button" class="btn btn-warning mb-2 ml-2 font-weight-bold d-flex flex-column align-items-center justify-content-center" id="numerasi-button" style="width: 400px; height: 100px;">
                                        <i class="fas fa-calculator fa-2x mb-1"></i>
                                        Numerasi
                                    </button>
                                </div>

                                <div id="extra-buttons" class="mt-2 mb-2" style="display: none;">

                                    @if($kelass->isNotEmpty())
                                        <div class="d-flex flex-wrap ">
                                            @foreach($kelass as $kelas)
                                                @if($kelas->guru_id == Auth::id()) 
                                                    <button type="button" class="btn show-siswa mt-2 ml-2 font-weight-bold" data-kelas="{{ $kelas->id}}" data-sekolah="{{ $kelas->nama_sekolah }}">
                                                        Kelas {{ $kelas->id_kelas }} - ({{ $kelas->nama_sekolah }})
                                                    </button>
                                                @endif
                                            @endforeach
                                        </div>
                                    @else
                                        <p>Tidak ada data kelas.</p>
                                    @endif
                                
                                </div>

                                
                                <div id="extra-buttons-2" class="mt-2 mb-2 overflow-auto" style="display: block;">

                                </div>

                                <div class="modal fade" id="jawabanModal" tabindex="-1" role="dialog" aria-labelledby="jawabanModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-xl" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="jawabanModalLabel">Detail Jawaban Siswa</h5>
                                                <button type="button" class="close" onclick="$('#jawabanModal').modal('hide');" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body overflow-y-auto" id="modal-content">
                                                <h2>{{$siswa->name ?? 'Belum ada siswa.'}}: </h2>
                                                <table class="table table-bordered table-responsive">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">No</th>
                                                            <th class="text-center">Jawaban</th>
                                                            <th class="text-center">Aksi</th>
                                                            <th class="text-center">Status Jawaban</th>
                                                            <th class="text-center">Aksi</th>
                                                            <th class="text-center">Alasan</th>
                                                            <th class="text-center">Aksi</th>
                                                            <th class="text-center">Status Alasan</th>
                                                            <th class="text-center">Aksi</th>
                                                            <th class="text-center">Komentar Guru</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="jawabanTableBody">
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" onclick="$('#jawabanModal').modal('hide');">Tutup</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- modal komentar -->
                                <div class="modal fade" id="komentarModal" tabindex="-1" role="dialog" aria-labelledby="komentarModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="komentarModalLabel">Komentar untuk <span id="komentarSiswaName"></span></h5>
                                                <button type="button" class="close" onclick="$('#komentarModal').modal('hide'); $('#jawabanModal').modal('show');" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <textarea class="form-control" id="komentarText" rows="5" placeholder="Masukkan komentar..."></textarea>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" onclick="$('#komentarModal').modal('hide'); $('#jawabanModal').modal('show');">Kembali</button>
                                                <button type="button" class="btn btn-primary" id="saveCommentButton">Simpan</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Skrip jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <!-- Skrip Popper JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <!-- Skrip Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>

        $(document).ready(function() {
            // insiasikan jenis yang dipilih
            let selectedJenis = '';

            // fungsi untuk mengubah tombol numerasi utama.

            $('#numerasi-button').click(function() {
                selectedJenis = 'numerasi';
                $('#extra-buttons').show(); 
                $('#extra-buttons-2').hide();
                $('.show-modal').removeClass('btn-primary btn-info').addClass('btn-warning');
                $('.show-siswa').removeClass('btn-primary btn-info').addClass('btn-warning');
            });
        
            // fungsi untuk merubah tombol literasi utama.

            $('#literasi-button').click(function() {
                selectedJenis = 'literasi';
                $('#extra-buttons').show();
                $('#extra-buttons-2').hide();  
                $('.show-modal').removeClass('btn-warning btn-info').addClass('btn-success');
                $('.show-siswa').removeClass('btn-warning btn-info').addClass('btn-success');
            });

            // kode menampilkan modal numerasi atau literasi

            $(document).on('click', '.show-modal', function() {
                const idSiswa = $(this).data('id');
                const name = $(this).data('name');

                $.ajax({
                    url: '{{ route('guru.kelola-jawaban.post') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        jenis: selectedJenis,
                        id_siswa: idSiswa 
                    },
                    success: function(data) {
                        let htmlContent = '';
                        if (data.length === 0) {
                            htmlContent += `
                                <tr>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                </tr>
                            `;
                        } else {
                            data.forEach(function(item, index) {
                                htmlContent += `
                                    <tr>
                                        <td class="text-center">${index + 1}</td>
                                        <td class="text-left">${item.jawaban_siswa}</td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-success update-score" data-type="skor_jawaban_siswa" data-id="${idSiswa}" data-soal="${item.id_soal}" data-value="1">Benar</button>
                                            <button class="btn btn-sm btn-danger mt-2 update-score" data-type="skor_jawaban_siswa" data-id="${idSiswa}" data-soal="${item.id_soal}" data-value="0">Salah</button>
                                        </td>
                                        <td class="text-center">${item.status_jawaban}</td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-success update-score" data-type="skor_yakin_jawaban" data-id="${idSiswa}" data-soal="${item.id_soal}" data-value="1">Benar</button>
                                            <button class="btn btn-sm btn-danger mt-2 update-score" data-type="skor_yakin_jawaban" data-id="${idSiswa}" data-soal="${item.id_soal}" data-value="0">Salah</button>
                                        </td>
                                        <td class="text-left">${item.alasan_siswa}</td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-success update-score" data-type="skor_alasan" data-id="${idSiswa}" data-soal="${item.id_soal}" data-value="1">Benar</button>
                                            <button class="btn btn-sm btn-danger mt-2 update-score" data-type="skor_alasan" data-id="${idSiswa}" data-soal="${item.id_soal}" data-value="0">Salah</button>
                                        </td>
                                        <td class="text-center">${item.status_alasan}</td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-success update-score" data-type="skor_yakin_alasan" data-id="${idSiswa}" data-soal="${item.id_soal}" data-value="1">Benar</button>
                                            <button class="btn btn-sm btn-danger mt-2 update-score" data-type="skor_yakin_alasan" data-id="${idSiswa}" data-soal="${item.id_soal}" data-value="0">Salah</button>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-primary comment-button" data-id="${idSiswa}" data-soal="${item.id_soal}" data-name="${name}">Komentar</button>
                                        </td>
                                    </tr>
                                `;
                            });
                        }
                        $('#modal-content h2').text(`${name}: `);
                        $('#jawabanTableBody').html(htmlContent);
                        $('#jawabanModal').modal('show');
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                    }
                });
            });


            // menampilkan list siswa pada kelas
            $('.show-siswa').click(function() {
                const idKelas = $(this).data('kelas');
                const namaSekolah = $(this).data('sekolah');

                $.ajax({
                    url: '{{ route("guru.get-siswa-by-kelas") }}',
                    method: 'GET',
                    data: {
                        id_kelas: idKelas
                    },
                    success: function(data) {
                        let htmlContent = '';

                        if (data.siswas.length > 0) {
                            data.siswas.forEach(function(siswa) {
                                htmlContent += `
                                    <button type="button" class="btn btn-primary d-block button-siswa show-modal mt-2 ml-2 font-weight-bold" data-id="${siswa.id_siswa}" data-name="${siswa.name}">
                                        ${siswa.name} - ${siswa.id_kelas}
                                    </button>
                                `;
                            });
                        } else {
                            htmlContent = '<p>Tidak ada siswa dalam kelas ini.</p>';
                        }

                        $('#extra-buttons-2').html(htmlContent).show();
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                    }
                });
            });

        
            // fugnsi untuk memberikan komentar

            $(document).on('click', '.comment-button', function(event) {
                event.preventDefault(); 
                const studentName = $(this).data('name');
                const idSiswa = $(this).data('id'); 
                const idSoal = $(this).data('soal'); 

                $('#komentarSiswaName').text(studentName);
                
                $.ajax({
                    url: '/get-komentar',
                    method: 'GET',
                    data: {
                        id_siswa: idSiswa,
                        id_soal: idSoal,
                    },
                    success: function(response) {
                        $('#komentarText').val(response.comments || '');
                        $('#jawabanModal').modal('hide'); 
                        $('#komentarModal').modal('show');
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                    }
                });

                $('#saveCommentButton').off('click').on('click', function() {
                    const komentar = $('#komentarText').val();
                    
                    $.ajax({
                        url: '/save-komentar',
                        method: 'POST',
                        data: {
                            id_siswa: idSiswa,
                            id_soal: idSoal,
                            komentar: komentar,
                            _token: '{{ csrf_token() }}' 
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Sukses!',
                                text: response.message, 
                                icon: 'success',
                                confirmButtonText: 'Oke'
                            });
                            $('#komentarModal').modal('hide');
                            $('#jawabanModal').modal('show');
                        },
                        error: function(xhr) {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Ada kesalahan mohon diperiksa kembali.',
                                icon: 'error',
                                confirmButtonText: 'Oke'
                            });
                            console.error(xhr.responseText);
                        }
                    });
                });
            });


            // fungsi untuk memberikan nilai pada database
            
            $(document).on('click', '.update-score', function(event) {
                event.preventDefault();
                const idSiswa = $(this).data('id');
                const idSoal = $(this).data('soal');
                const type = $(this).data('type');
                const value = $(this).data('value');
                const $buttons = $(`.update-score[data-id="${idSiswa}"][data-soal="${idSoal}"][data-type="${type}"]`);

                Swal.fire({
                    title: 'Anda yakin dengan pilihan ?',
                    text: "Anda tidak bisa mengubah pilihan ini setelah menyimpan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    cancelButtonText: 'Tidak',
                    confirmButtonText: 'Iya'
                }).then((result) => {
                    if (result.isConfirmed) {

                        $buttons.each(function() {
                            const idSiswa = $(this).data('id');
                            const idSoal = $(this).data('soal');
                            const type = $(this).data('type');
                        });

                        $.ajax({
                            url: '{{ route('guru.update-skor-jawaban') }}',
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                id_siswa: idSiswa,
                                id_soal: idSoal,
                                type: type,
                                value: value
                            },
                            success: function(response) {
                                Swal.fire(
                                    'Tersimpan!',
                                    response.success,
                                    'success'
                                );

                                // disable dan ubah menjadi abu-abu
                                $buttons.attr('disabled', true).removeClass('btn-success btn-danger').addClass('btn-secondary');
                            },
                            error: function(xhr) {
                                console.error(xhr.responseText);
                            }
                        });
                    }
                });
            });

            // kode alternatif

            // $(document).on('click', '.update-score', function(event) {
            //     event.preventDefault();
            //     const idSiswa = $(this).data('id');
            //     const idSoal = $(this).data('soal');
            //     const type = $(this).data('type');
            //     const value = $(this).data('value');

            //     $.ajax({
            //         url: '{{ route('guru.update-skor-jawaban') }}',
            //         method: 'POST',
            //         data: {
            //             _token: '{{ csrf_token() }}',
            //             id_siswa: idSiswa,
            //             id_soal: idSoal,
            //             type: type,
            //             value: value
            //         },
            //         success: function(response) {
            //             alert(response.success);
            //         },
            //         error: function(xhr) {
            //             console.error(xhr.responseText);
            //         }
            //     });
            // });

        });

    </script>

    @endsection