@extends('layouts.app')

    @section('header')
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Halaman Guru: Input Soal') }}
        </h2>
    @endsection

    @section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="container mt-3">
                    <div class="row">
                        <div class="col-md-10">
                            @if (session('error'))
                                <div class="alert alert-danger">
                                    {{ session('error') }}
                                </div>
                            @endif

                            <form  id="soalForm" action="{{ route('soal.store') }}" method="post">
                                @csrf
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label for="jenis">Jenis Soal</label>
                                        <select name="jenis" id="jenis" class="form-control">
                                            <option value="literasi">Literasi</option>
                                            <option value="numerasi">Numerasi</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="kelas_id">Pilih Kelas</label>
                                        <select name="kelas_id" id="kelas_id" class="form-control">
                                            @if($kelass->isEmpty())
                                                <option value="" disabled selected>Tidak ada kelas</option>
                                            @else
                                                @foreach($kelass as $kelas)
                                                    <option value="{{ $kelas->id }}">{{ $kelas->id_kelas }} - {{ $kelas->nama_sekolah }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="waktu">Waktu dalam detik</label>
                                        <input type="number" name="waktu" id="waktu" class="form-control" placeholder="Masukan waktu dalam detik" min="1">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="content">Input Soal</label>
                                    <textarea name="pertanyaan" id="pertanyaan" class="form-control editor"></textarea>
                                </div>
                                <input type="hidden" name="status" value="on"> 
                                <button type="submit" class="btn btn-primary font-weight-bold">Submit</button>
                            </form>
                        </div>
                        <div class="col-md-10 mt-2 mb-3 ml-12 justify-content-center">
                            <div class="card">
                                <div class="card-header">
                                    <h2>Bank Soal</h2>
                                
                                    <form action="{{ route('soal.filter') }}" method="GET">
                                        <div class="form-row">
                                            <div class="form-group col-md-4">
                                                <label for="kelas_id">Pilih Kelas</label>
                                                <select name="kelas_id" id="kelas_id" class="form-control">
                                                    @foreach($kelass as $kelas)
                                                        <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>
                                                            {{ $kelas->id_kelas }} - {{ $kelas->nama_sekolah }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        
                                            <div class="form-group col-md-4">
                                                <label for="jenis">Pilih Jenis Soal</label>
                                                <select name="jenis" id="jenis" class="form-control">
                                                    <option value="literasi" {{ request('jenis') == 'literasi' ? 'selected' : '' }}>Literasi</option>
                                                    <option value="numerasi" {{ request('jenis') == 'numerasi' ? 'selected' : '' }}>Numerasi</option>
                                                </select>
                                            </div>
                                        
                                            <div class="form-group col-md-4 d-flex align-items-end">
                                                <button type="submit" class="btn btn-success mr-2 font-weight-bold">Pilih</button>
                                                <button type="button" data-toggle="modal" data-target="#previewModal"
                                                 id="previewButton" class="btn btn-warning font-weight-bold">Preview</button>
                                            </div>
                                        </div>                                        
                                    </form>
                                </div>
                                
                                <div class="card-body overflow-auto">
                                    @if(isset($soals) && $soals->isEmpty())
                                        <p>Tidak ada soal yang tersedia.</p>
                                    @else 
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">No.</th>
                                                    <th class="text-center">Pertanyaan</th>
                                                    <th class="text-center">Jenis</th>
                                                    <th class="text-center">Waktu (Detik)</th> 
                                                    <th class="text-center">Status</th>
                                                    <th class="text-center">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($soals as $index => $soal)
                                                    <tr>
                                                        <td class="text-center">{{ $index + 1 }}</td>
                                                        <td>{!! $soal->pertanyaan !!}</td>
                                                        <td class="text-center">{{ ucfirst($soal->jenis) }}</td>
                                                        <td class="text-center">{{ $soal->waktu }} detik</td>
                                                        <td class="text-center">{{ ucfirst($soal->status) }}</td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <button type="button" 
                                                                    class="btn btn-warning btn-sm ml-2" 
                                                                    data-toggle="modal" 
                                                                    data-target="#editModal" 
                                                                    data-id="{{ $soal->id }}" 
                                                                    data-pertanyaan="{{ $soal->pertanyaan }}" 
                                                                    data-waktu="{{ $soal->waktu }}"
                                                                    data-image="{{ $soal->image_url }}">
                                                                    Edit
                                                                </button>

                                                                <form action="{{ route('soal.delete', $soal->id) }}" method="POST"
                                                                    class="inline delete-form">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger btn-sm ml-2">Hapus</button>
                                                                </form>

                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @endif
                                </div>
                            </div>
                        </div>                                            
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Soal</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editId">
                    <textarea id="editPertanyaan"></textarea>
                    {{-- <img id="imagePreview" src="" alt="Image Preview" style="max-width: 100%; height: auto;"> --}}
                    <div class="form-group mt-3">
                        <label for="editDetik">Waktu dalam Detik</label>
                        <input type="number" id="editDetik" class="form-control" placeholder="Masukkan waktu dalam detik" min="1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveChanges">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Preview-->
    <div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="previewModalLabel">Preview Soal</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body overflow-y-auto">
                    <div id="previewContent">

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- skrip untuk jquery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <!-- skrip untuk ckeditor -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.9.2/ckeditor.js" integrity="sha512-OF6VwfoBrM/wE3gt0I/lTh1ElROdq3etwAquhEm2YI45Um4ird+0ZFX1IwuBDBRufdXBuYoBb0mqXrmUA2VnOA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!-- skrip plugin wiris-->
    <script src="https://cdn.jsdelivr.net/npm/@wiris/mathtype-ckeditor4@7.24.0/plugin.js"></script>

    <!-- skrip untuk popper js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>

    <!-- skrip untuk bootstrsap -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <script>
        
        $('#editModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); 
            var id = button.data('id'); 
            var pertanyaan = button.data('pertanyaan');
            var waktu = button.data('waktu'); 
            var image = button.data('image'); 

            var modal = $(this);
            modal.find('#editId').val(id);
            modal.find('#editPertanyaan').val(pertanyaan);
            modal.find('#editDetik').val(waktu);
            modal.find('#imagePreview').attr('src', image); 

            if (CKEDITOR.instances['editPertanyaan']) {
                CKEDITOR.instances['editPertanyaan'].destroy();
            }

            CKEDITOR.replace('editPertanyaan', {
                extraPlugins: 'ckeditor_wiris',
                toolbar: [
                    { name: 'clipboard', items: ['Undo', 'Redo'] },
                    { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline'] },
                    { name: 'insert', items: ['ckeditor_wiris_formulaEditor', 'ckeditor_wiris_formulaEditorChemistry', 'Image', 'Table'] },
                    { name: 'paragraph', items: ['NumberedList', 'BulletedList', 'Blockquote'] },
                    { name: 'tools', items: ['HorizontalRule'] }
                ],
                contentsCss: 'https://www.wiris.net/demo/plugins/ckeditor/css/mathType.css',
                filebrowserUploadUrl: "{{ route('ckeditor.upload', ['_token' => csrf_token()]) }}",
                filebrowserUploadMethod: 'form',
                image2_alignClasses: ['image-align-left', 'image-align-center', 'image-align-right'],
                image2_disableResizer: false,
            });
        });
    
        $('#saveChanges').click(function() {
            var form = $('#editForm');
            var url = '{{ route('soal.update', ':id') }}';
            url = url.replace(':id', $('#editId').val());

            var waktu = $('#editDetik').val();
    
            $.ajax({
                type: "POST",
                url: url,
                data: {
                    _token: '{{ csrf_token() }}',
                    pertanyaan: CKEDITOR.instances['editPertanyaan'].getData(),
                    waktu: waktu,
                    _method: 'PUT'
                },
                success: function(response) {
                    location.reload();
                },
                error: function(xhr) {
                    alert('Error updating the question.');
                }
            });
        });

        // swal alert untuk hapus dan menambahkan soal 
        $(document).on('submit', '.delete-form', function(e) {
            e.preventDefault(); 

            var form = this; 

            Swal.fire({
                title: 'Apakah kamu yakin?',
                text: "Data yang sudah dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit(); 
                }
            })
        });

        $('#soalForm').on('submit', function(e) {
            e.preventDefault(); 
            
            var pertanyaanContent = CKEDITOR.instances['pertanyaan'].getData().trim();

            if (pertanyaanContent === '') {
                Swal.fire({
                    title: 'Kolom Pertanyaan Kosong!',
                    text: 'Mohon isi kolom pertanyaan sebelum mengirimkan form.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
            } else {
                Swal.fire({
                    title: 'Apakah kamu yakin?',
                    text: "Kamu akan mengirimkan pertanyaan ini.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, kirim!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit(); 
                    }
                });
            }
        });

                
        $('#previewButton').on('click', function() {
            var previewContent = '';
            var soals = @json($soals); 

            if (soals.length > 0) {
                soals.forEach(function(soal, index) {
                    previewContent += `
                        <div class="mb-4">
                            <p><strong>Soal ${index + 1}</strong></p>
                            <p>${soal.pertanyaan}</p>
                            <label for="jawaban" class="ml-3">Jawaban:</label>
                            <textarea class="form-control" name="jawaban" rows="3" disabled></textarea>
                        </div>
                        <hr>
                    `;
                });
            } else {
                previewContent = '<p>Tidak ada soal yang tersedia sesuai filter.</p>';
            }

            $('#previewContent').html(previewContent); 
        });
        
    </script>

    <script>
        // penambahan icon pada plugin
        CKEDITOR.plugins.addExternal('ckeditor_wiris', 'https://cdn.jsdelivr.net/npm/@wiris/mathtype-ckeditor4@7.24.0/', 'plugin.js');
        
        // inisiasi ckeditor
        CKEDITOR.replace('pertanyaan', {
    
                extraPlugins: 'ckeditor_wiris',
                toolbar: [
                    { name: 'clipboard', items: ['Undo', 'Redo'] },
                    { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline'] },
                    { name: 'insert', items: ['ckeditor_wiris_formulaEditor', 'ckeditor_wiris_formulaEditorChemistry', 'Image', 'Table'] },
                    { name: 'paragraph', items: ['NumberedList', 'BulletedList', 'Blockquote'] },
                    { name: 'tools', items: ['HorizontalRule'] }
                ],
                contentsCss: 'https://www.wiris.net/demo/plugins/ckeditor/css/mathType.css',
                filebrowserUploadUrl: "{{ route('ckeditor.upload', ['_token' => csrf_token()]) }}",
                filebrowserUploadMethod: 'form',
                image2_alignClasses: ['image-align-left', 'image-align-center', 'image-align-right'],
                image2_disableResizer: false,

            });

        // render fungsi matematika
        function renderMath() {
            if (window.MathJax) {
                MathJax.Hub.Queue(["Typeset", MathJax.Hub]);
            }
        }

        // lanjutan render
        CKEDITOR.instances.pertanyaan.on('change', function() {
            renderMath();
        });
        
    </script>

    @endsection
