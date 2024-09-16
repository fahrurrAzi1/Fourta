@extends('layouts.jawab')

    @section('header')
        <title>Jawab Pertanyaan</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @endsection

    @section('content')

    <div class="container mt-2">
        <div class="row">
            <div class="col-md-12">
                <h4>Soal {{ ucfirst(session('jenis')) }}</h4>
                @if($soals->count() > 0)
                <div id="timerContainer" class="mb-3">
                    <div class="alert alert-info text-center p-2 rounded animated fadeIn">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="font-weight-bold">Waktu Tersisa:</span>
                            <span class="d-flex align-items-center">
                                <i class="fas fa-clock mr-2 pulse"></i>
                                <span class="badge badge-light font-weight-bold" style="font-size: 1.25rem;" id="timer">00:00</span>
                            </span>
                        </div>
                    </div>
                </div>

                <style>
                    @keyframes pulse {
                        0% {
                            transform: scale(1);
                        }
                        50% {
                            transform: scale(1.1);
                        }
                        100% {
                            transform: scale(1);
                        }
                    }
                
                    .pulse {
                        animation: pulse 1s infinite;
                    }
                
                    .animated {
                        animation-duration: 1s;
                    }
                
                    .fadeIn {
                        animation-name: fadeIn;
                    }
                
                    @keyframes fadeIn {
                        from {
                            opacity: 0;
                        }
                        to {
                            opacity: 1;
                        }
                    }
                </style>
                @endif              
                <div id="soalContainer">
                    @if($soals->count() > 0)
                        @php
                            $currentSoalIndex = session('current_soal_index', 0);
                            $currentSoal = $soals[$currentSoalIndex];
                        @endphp
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="mb-2">
                                    <p><strong>Soal {{ $currentSoalIndex + 1 }}</strong></p>
                                    <div id="pertanyaanContainer">{!! $currentSoal->pertanyaan !!}</div>
                                </div>
                                <form id="jawabanForm" method="POST" action="{{ route('jawaban.submit') }}">
                                    @csrf
                                    <div class="form-group row">
                                        <label for="jawaban" class="ml-3">Jawaban:</label>
                                        <div class="col-sm-10">
                                            <textarea id="inputJawabSoal" name="jawaban" rows="3"></textarea>
                                            <input type="hidden" name="id_soal" value="{{ $currentSoal->id }}">
                                            <input type="hidden" name="id_siswa" value="{{ session('id_siswa', 'kosong') }}">
                                            <input type="hidden" name="kelas_id" value="{{ $currentSoal->kelas_id }}">
                                            <input type="hidden" name="current_soal_index" value="{{ $currentSoalIndex }}">
                                            <input type="hidden" name="waktu" value="{{ $currentSoal->waktu }}">
                                            <button type="button" id="kirimjawab" class="btn btn-primary mt-2" onclick="showStatusJawabanModal()">Kirim Jawaban</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @else
                        <p id="noSoalMessage">Tidak ada pertanyaan untuk ditampilkan.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk status jawaban -->
    <div class="modal fade" id="statusJawabanModal" tabindex="-1" role="dialog" aria-labelledby="statusJawabanModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="statusJawabanModalLabel">Tingkat Keyakinan Jawaban</h5>
                    <button type="button" class="close" id="statusjawab" onclick="$('#statusJawabanModal').modal('hide')" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Apakah anda yakin dengan jawaban anda?</p>
                    <div>
                        <label><input type="radio" name="statusJawaban" value="tidak"> 1 (Tidak)</label><br>
                        <label><input type="radio" name="statusJawaban" value="yakin"> 2 (Yakin)</label><br>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="showAlasanModal()">Lanjut</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk alasan menjawab -->
    <div class="modal fade" id="alasanModal" tabindex="-1" role="dialog" aria-labelledby="alasanModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="alasanModalLabel">Alasan Menjawab</h5>
                    <button type="button" class="close" id="alasjawab" onclick="$('#alasanModal').modal('hide')" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Apa alasan Anda memilih jawaban ini?</p>
                    <textarea id="alasanSiswa" class="form-control mb-3"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="showStatusAlasanModal()">Lanjut</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk keyakinan alasan -->
    <div class="modal fade" id="statusAlasanModal" tabindex="-1" role="dialog" aria-labelledby="statusAlasanModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="statusAlasanModalLabel">Keyakinan dengan alasan</h5>
                    <button type="button" class="close" id="statusalasjawab" onclick="$('#statusAlasanModal').modal('hide')" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Apakah anda yakin dengan alasan anda?</p>
                    <div>
                        <label><input type="radio" name="statusAlasan" value="tidak"> 1 (Tidak)</label><br>
                        <label><input type="radio" name="statusAlasan" value="yakin"> 2 (Yakin)</label><br>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="submitForm()">Submit</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- skrip untuk ckeditor -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.9.2/ckeditor.js" integrity="sha512-OF6VwfoBrM/wE3gt0I/lTh1ElROdq3etwAquhEm2YI45Um4ird+0ZFX1IwuBDBRufdXBuYoBb0mqXrmUA2VnOA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!-- skrip plugin wiris-->
    <script src="https://cdn.jsdelivr.net/npm/@wiris/mathtype-ckeditor4@7.24.0/plugin.js"></script>

    <!-- skrip untuk popper js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>

    <!-- skrip untuk bootstrsap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>

        var timer;
        var waktu = parseInt('{{ isset($currentSoal) ? $currentSoal->waktu : 0 }}', 10);
        var isTimerPaused = false;
        var remainingTime = waktu;
    
        function startTimer() {
            timer = setInterval(function() {
                if (!isTimerPaused) {
                    if (remainingTime <= 0) {
                        clearInterval(timer);
                        if (!document.getElementById('noSoalMessage')) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Waktu Habis!',
                                text: 'Waktu Anda telah habis. Soal akan berpindah.',
                                confirmButtonText: 'OK'
                                }).then(() => {
                                    moveToNextSoal(); 
                                });
                        }
                    } else {
                        remainingTime--;
                        displayTime();
                    }
                }
            }, 1000);
        }
    
        function pauseTimer() {
            isTimerPaused = true;
        }
    
        function resumeTimer() {
            isTimerPaused = false;
        }
    
        function displayTime() {
            var minutes = Math.floor(remainingTime / 60);
            var seconds = remainingTime % 60;
            if (seconds < 10) {
                seconds = "0" + seconds;
            }
            $('#timer').text(minutes + ":" + seconds);
        }
    
        $(document).ready(function() {
            displayTime(); 
            startTimer(); 
    
            $('#kirimjawab').on('click', function () {
                pauseTimer(); 
            });

            $('#statusjawab').on('click', function () {
                resumeTimer(); 
            });

            $('#alasjawab').on('click', function () {
                resumeTimer(); 
            });

            $('#statusalasjawab').on('click', function () {
                resumeTimer(); 
            });


        });
    
        function showStatusJawabanModal() {
            $('#statusJawabanModal').modal('show');
        }
    
        function showAlasanModal() {
            $('#statusJawabanModal').modal('hide');
            $('#alasanModal').modal('show');
        }
    
        function showStatusAlasanModal() {
            $('#alasanModal').modal('hide');
            $('#statusAlasanModal').modal('show');
        }
    
        function submitForm() {
            $('#statusAlasanModal').modal('hide');
    
            const idSoal = $('input[name="id_soal"]').val();
            const kelasId = $('input[name="kelas_id"]').val();
            // const jawabanSiswa = $('textarea[name="jawaban"]').val();
            const jawabanSiswa = CKEDITOR.instances['inputJawabSoal'].getData(); 
            const jawabanSiswaText = $('<div>').html(jawabanSiswa).text();
            const statusJawaban = $('input[name="statusJawaban"]:checked').val();
            const alasanSiswa = $('#alasanSiswa').val();
            const statusAlasan = $('input[name="statusAlasan"]:checked').val();
            const currentSoalIndex = parseInt($('input[name="current_soal_index"]').val());
    
            const idSiswa = '{{ session("id_siswa", null) }}';
    
            $.ajax({
                url: '{{ route("jawaban.submit") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id_soal: idSoal,
                    id_siswa: idSiswa,
                    kelas_id: kelasId,
                    jawaban_siswa: jawabanSiswaText,
                    status_jawaban: statusJawaban,
                    alasan_siswa: alasanSiswa,
                    status_alasan: statusAlasan,
                    current_soal_index: currentSoalIndex
                },
                success: function(response) {
                    if (response.finished) {
                        window.location.href = "{{ route('siswa.selesai-jawab') }}"; 
                    } else {
                        const nextSoal = response.nextSoal;
                        if (nextSoal) {
                            $('input[name="current_soal_index"]').val(currentSoalIndex + 1);
                            $('input[name="id_soal"]').val(nextSoal.id);
                            $('#pertanyaanContainer').html(nextSoal.pertanyaan);
                            // $('textarea[name="jawaban"]').val('');
                            CKEDITOR.instances['inputJawabSoal'].setData(''); 
                            $('input[name="statusJawaban"]').prop('checked', false); 
                            $('#alasanSiswa').val('');
                            $('input[name="statusAlasan"]').prop('checked', false); 
                            $('p strong').text('Soal ' + (currentSoalIndex + 2)); 
                            remainingTime = nextSoal.waktu;
                            displayTime();
                            resumeTimer(); 
                        } else {
                            window.location.href = "{{ route('siswa.selesai-jawab') }}"; 
                        }
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                }
            });
        }
    
        function moveToNextSoal() {
            const currentSoalIndex = parseInt($('input[name="current_soal_index"]').val());
    
            $.ajax({
                url: '{{ route("jawaban.getNextSoal") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id_soal: $('input[name="id_soal"]').val(),
                    current_soal_index: currentSoalIndex
                },
                success: function(response) {
                    if (response.finished) {
                        window.location.href = "{{ route('siswa.selesai-jawab') }}"; 
                    } else {
                        const nextSoal = response.nextSoal;
                        if (nextSoal) {
                            $('input[name="current_soal_index"]').val(response.currentSoalIndex);
                            $('input[name="id_soal"]').val(nextSoal.id);
                            $('#pertanyaanContainer').html(nextSoal.pertanyaan);
                            $('textarea[name="jawaban"]').val(''); 
                            $('input[name="statusJawaban"]').prop('checked', false); 
                            $('#alasanSiswa').val('');
                            $('input[name="statusAlasan"]').prop('checked', false); 
                            $('p strong').text('Soal ' + (response.currentSoalIndex + 1)); 
                            remainingTime = nextSoal.waktu;
                            displayTime();
                            startTimer();
                        } else {
                            window.location.href = "{{ route('siswa.selesai-jawab') }}"; 
                        }
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                }
            });
        }

    </script>

    <script>

        CKEDITOR.plugins.addExternal('ckeditor_wiris', 'https://cdn.jsdelivr.net/npm/@wiris/mathtype-ckeditor4@7.24.0/', 'plugin.js');

        CKEDITOR.replace('inputJawabSoal', {
            extraPlugins: 'ckeditor_wiris',
            toolbar: [
                { name: 'clipboard', items: ['Undo', 'Redo'] },
                { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline'] },
                { name: 'insert', items: ['ckeditor_wiris_formulaEditor', 'ckeditor_wiris_formulaEditorChemistry'] },
                { name: 'paragraph', items: ['NumberedList', 'BulletedList', 'Blockquote'] },
                { name: 'tools', items: ['HorizontalRule'] }
            ],
            contentsCss: 'https://www.wiris.net/demo/plugins/ckeditor/css/mathType.css',
        });

        // render fungsi matematika
        function renderMath() {
            if (window.MathJax) {
                MathJax.Hub.Queue(["Typeset", MathJax.Hub]);
            }
        }

        // lanjutan render
        CKEDITOR.instances.inputJawabSoal.on('change', function() {
            renderMath();
        });

    </script>

    @endsection
