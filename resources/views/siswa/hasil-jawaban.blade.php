@extends('layouts.app')

    @section('header')
        <h3 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Halaman Siswa: Presentase Hasil Siswa') }}
        </h3>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    @endsection

    @section('content')
    @php
        $studentId = Auth::user()->id_siswa; 
    @endphp
    <div class="py-12">
        <div>
            <div class="bg-white overflow-y-auto shadow-sm sm:rounded-lg">
                <div class="p-5 d-flex table-responsive">
                    
                    <table id="hasilSiswaTable" class="table table-bordered table-lg text-center w-100">
                        <thead class="bg-light">
                            <th class="align-middle text-center">No</th>
                            <th class="align-middle text-center">Nama</th>
                            <th class="align-middle text-center">Jenis</th>
                            <th class="align-middle text-center">Sekolah</th>
                            <th class="align-middle text-center">Kelas</th>
                            @for ($i = 1; $i <= 10; $i++)
                                <th>Komentar Soal {{ $i }}</th>
                            @endfor
                            <th class="align-middle text-center">Total Skor</th>
                            <th class="align-middle text-center">Kategori</th>
                        </thead>
                        
                        <tbody id="hasilSiswaTableBody" class="overflow-y-auto">
                        </tbody>

                    </table>
                </div>

                <canvas id="hasilSiswaChart" class="p-5" width="150" height="100"></canvas>

            </div>
        </div>
    </div>

    <!-- Skrip jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <!-- Skrip Popper JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <!-- Skrip Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Skrip DataTables -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <!-- Skrip Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Skrip untuk ekspor pdf dan excel -->
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>

    <script>

        function loadScript(url) {
            return new Promise((resolve, reject) => {
                const script = document.createElement('script');
                script.src = url;
                script.onload = resolve;
                script.onerror = reject;
                document.head.appendChild(script);
            });
        }

        $(document).ready(function() {

            var studentId = "{{ $studentId }}";

            var selectedJenis = '';
            var selectedKelas = '';
    
            var table = $('#hasilSiswaTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('datatable.hasil') }}",
                    data: function(e) {
                        e.jenis = selectedJenis;
                        e.kelas = selectedKelas;
                        e.studentId = studentId;
                    },
                },
                columns: [
                    { data: 'no', name: 'no' },
                    { data: 'nama', name: 'nama' },
                    { data: 'jenis', name: 'jenis' },
                    { data: 'sekolah', name: 'sekolah' },
                    { data: 'kelas', name: 'kelas' },
                    @for ($i = 1; $i <= 10; $i++)
                        { data: 'q{{ $i }}_komentar', name: 'q{{ $i }}_komentar' },
                    @endfor
                    { data: 'skor_total', name: 'skor_total' },
                    { data: 'kategori_skor', name: 'kategori_skor' },
                ],
                dom: "<'row'<'col-sm-12 col-md-6 mb-2'lB><'col-sm-12 col-md-6'f>>" + 
                     "<'row'<'col-sm-12'tr>>" + 
                     "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                buttons: [
                    {
                        text: '<i class="fas fa-file-excel"></i>&nbsp;Excel',
                        className: 'btn btn-sm btn-outline-success',
                        titleAttr: 'Ekspor ke Excel',
                        action: async function (e, dt, button, config) {

                            await loadScript('https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js');

                            const studentData = await fetch(`/api/get-student-data/${studentId}`).then(response => response.json());
                            
                            const tableData = dt.buttons.exportData({
                                columns: [0, 1, 5, 6, 7]
                            });

                            const workbook = new ExcelJS.Workbook();
                            const worksheet = workbook.addWorksheet('Sheet1');

                            worksheet.addRow(['NIS:', studentData.nis]).eachCell((cell) => {
                                cell.alignment = { horizontal: 'left' };
                            });

                            worksheet.addRow(['Nama:', studentData.nama]).eachCell((cell) => {
                                cell.alignment = { horizontal: 'left' };
                            });

                            worksheet.addRow(['Email:', studentData.email]).eachCell((cell) => {
                                cell.alignment = { horizontal: 'left' };
                            });

                            worksheet.addRow(['Sekolah:', studentData.sekolah]).eachCell((cell) => {
                                cell.alignment = { horizontal: 'left' };
                            });

                            worksheet.addRow(['Kelas:', studentData.kelas]).eachCell((cell) => {
                                cell.alignment = { horizontal: 'left' };
                            });

                            worksheet.addRow([]);

                            const headerRow = worksheet.addRow(tableData.header);

                            headerRow.eachCell((cell) => {
                                cell.fill = {
                                    type: 'pattern',
                                    pattern: 'solid',
                                    fgColor: { argb: 'FF000000' } 
                                };
                                cell.font = { bold: true, color: { argb: 'FFFFFFFF' } };
                                cell.alignment = { vertical: 'middle', horizontal: 'center', wrapText: true };
                                cell.border = {
                                    top: { style: 'thin' },
                                    left: { style: 'thin' },
                                    bottom: { style: 'thin' },
                                    right: { style: 'thin' }
                                };
                            });

                            tableData.body.forEach(rowData => {
                                const row = worksheet.addRow(rowData);
                                row.eachCell((cell) => {
                                    cell.fill = {
                                        type: 'pattern',
                                        pattern: 'solid',
                                        fgColor: { argb: 'FFF8F9FA' } 
                                    };
                                    cell.alignment = { vertical: 'middle', horizontal: 'center', wrapText: true };
                                    cell.border = {
                                        top: { style: 'thin' },
                                        left: { style: 'thin' },
                                        bottom: { style: 'thin' },
                                        right: { style: 'thin' }
                                    };
                                });
                            });

                            worksheet.columns.forEach((column) => {
                                let maxLength = 0;
                                column.eachCell({ includeEmpty: true }, (cell) => {
                                    const columnLength = cell.value ? cell.value.toString().length : 10;
                                    if (columnLength > maxLength) {
                                        maxLength = columnLength;
                                    }
                                });
                                
                                column.width = Math.max(maxLength + 2, column.width || 10); 
                            });

                            const buffer = await workbook.xlsx.writeBuffer();
                            saveAs(new Blob([buffer]), 'Laporan_Hasil_Siswa.xlsx');
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="fas fa-file-pdf"></i>&nbsp;PDF',
                        className: 'btn btn-sm btn-outline-danger',
                        titleAttr: 'Ekspor ke PDF',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        exportOptions: {
                            columns: [0, 1, 5, 6, 7],
                        },
                        filename: function() {
      
                            return 'Laporan_Hasil_Siswa';
                        
                        },
                        customize: function (doc) {
                            $.ajax({
                                url: '/api/get-student-data/' + studentId,
                                method: 'GET',
                                async: false,
                                success: function(studentData) {
                                    doc.content.splice(0, 0, {
                                        margin: [0, 0, 0, 12],
                                        alignment: 'left',
                                        stack: [
                                            { text: 'NIS: ' + studentData.nis, margin: [0, 0, 0, 6], alignment: 'left' },
                                            { text: 'Nama: ' + studentData.nama, margin: [0, 0, 0, 6], alignment: 'left' },
                                            { text: 'Email: ' + studentData.email, margin: [0, 0, 0, 6], alignment: 'left' },
                                            { text: 'Sekolah: ' + studentData.sekolah, margin: [0, 0, 0, 6], alignment: 'left' },
                                            { text: 'Kelas: ' + studentData.kelas, margin: [0, 0, 0, 6], alignment: 'left' }
                                        ]
                                    });
                                }
                            });

                            doc.content = doc.content.filter(item => {
                                return !(item.text && item.text.includes('4TA-Literasi&Numerasi'));
                            });

                            if (doc.content[1]) {
                                doc.content[1].alignment = 'center';
                            }
                        }

                    },
                ],
                initComplete: function () {
                    $('div.dataTables_filter input').addClass('form-control');
                    $('div.dataTables_length').addClass('d-flex align-items-center');
                    $('div.dataTables_filter').addClass('d-flex justify-content-end align-items-center');

                    $('div.dataTables_length').append(`
                        <div class='d-flex p-2'>
                        
                            <!-- Filter Elements -->
                            <div class="ml-0 mt-3">
                                <button type="button" class="btn btn-success" id="applyFilter"><p class="font-weight-bold mb-0">Terapkan Filter</p></button>
                            </div>
                            <div class="ml-2 mt-1">
                                <label for="jenisSelect" class="form-label">Pilih Jenis:</label>
                                <select id="jenisSelect" class="p-2 form-select btn-warning rounded font-weight-bold">
                                    <option value="">Semua Jenis</option>
                                    <option value="literasi">Literasi</option>
                                    <option value="numerasi">Numerasi</option>
                                </select>
                            </div>
                            <div class="ml-2 mt-1">
                                <label for="kelasSelect" class="form-label">Pilih Kelas:</label>
                                <select id="kelasSelect" class="p-2 form-select btn-primary rounded font-weight-bold">
                                    <option value="">Semua Kelas</option>
                                    @foreach($kelass as $kelas)
                                        <option value="{{ $kelas->id_kelas }}">{{ $kelas->id_kelas }} - {{ $kelas->nama_sekolah }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                    `);
    
                    $('#applyFilter').click(function() {
                        selectedJenis = $('#jenisSelect').val();
                        selectedKelas = $('#kelasSelect').val();
                        applyFilters();
                    });
    
                    function applyFilters() {
                        table.column('jenis:name').search(selectedJenis);
                        table.column('kelas:name').search(selectedKelas);
                        table.draw();
                    }
                }
            });
    
            table.on('draw', function() {
                var data = table.rows({ filter: 'applied' }).data();
    
                if (data.length === 0) {
                    clearChart(); 
                } else {
                    var labels = [];
                    var chartData = [];
                    var colors = [];
                    var kategori = [];
                    var kelas = [];
    
                    data.each(function(row) {
                        labels.push(row.nama);
                        chartData.push(parseInt(row.skor_total));
                        colors.push(getRandomColor());
                        kategori.push(row.kategori_skor);
                        kelas.push(row.kelas);
                    });
    
                    updateChart(labels, chartData, colors, kategori, kelas); 
                }
            });
    
            function updateChart(labels, data, colors, kategori, kelas) {
                var ctx = document.getElementById('hasilSiswaChart').getContext('2d');
                if (window.siswaChart) {
                    window.siswaChart.destroy();
                }
                window.siswaChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Total Skor Siswa',
                            data: data,
                            backgroundColor: colors,
                            borderColor: colors,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            x: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Nama Siswa' 
                                }
                            },
                            y: {
                                beginAtZero: true,
                                max: 100 
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(tooltipItem) {
                                        var index = tooltipItem.dataIndex;
                                        return [
                                            'Nama: ' + labels[index],
                                            'Kelas: ' + kelas[index],
                                            'Total Skor: ' + data[index],
                                            'Kategori: ' + kategori[index],
                                        ];
                                    }
                                }
                            }
                        }
                    }
                });
            }
    
            function clearChart() {
                var ctx = document.getElementById('hasilSiswaChart').getContext('2d');
                if (window.siswaChart) {
                    window.siswaChart.destroy();
                }
                window.siswaChart = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: [],
                        datasets: [{
                            label: 'Total Skor Siswa',
                            data: [],
                            backgroundColor: [],
                            borderColor: [],
                            borderWidth: 1
                        }]
                    }
                });
            }
    
            function getRandomColor() {
                var letters = '0123456789ABCDEF';
                var color = '#';
                for (var i = 0; i < 6; i++) {
                    color += letters[Math.floor(Math.random() * 16)];
                }
                return color;
            }

            
            // alternatif kode
            // worksheet.columns.forEach((column) => {
            //     let maxLength = 0;
            //     column.eachCell({ includeEmpty: true }, (cell) => {
            //         const columnLength = cell.value ? cell.value.toString().length : 10;
            //         if (columnLength > maxLength) {
            //             maxLength = columnLength;
            //         }
            //     });
            //     column.width = Math.min(maxLength + 2, 30); 
            // });

        });

    </script>    

    @endsection

    