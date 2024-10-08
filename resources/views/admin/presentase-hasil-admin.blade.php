@extends('layouts.app')

    @section('header')
        <h3 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Halaman Admin: Presentase Hasil Siswa') }}
        </h3>
    @endsection

    @section('content')

    <div class="py-12">
        <div>
            <div class="bg-white overflow-y-auto shadow-sm sm:rounded-lg">
                <div class="p-2 d-flex table-responsive">
                    <table id="presentaseTableAdmin" class="table table-bordered table-lg text-center w-100">
                        <thead class="bg-light breaks-word">
                            <tr>
                                <th colspan="5" class="align-middle text-left">Keterangan Skor</th>
                                @for ($i = 1; $i <= 10; $i++)
                                    <th>Skor Jawaban</th>
                                    <th>Skor Yakin Jawaban</th>
                                    <th>Skor Alasan </th>
                                    <th>Skor Yakin Alasan</th>
                                    <th>Koreksi Guru</th>
                                @endfor                                
                                <th colspan="3" rowspan="2" class="align-middle text-center=">Keterangan Akhir</th>
                            </tr>
                            <tr>
                                <th colspan="5" class="align-middle text-left">Nomor Soal</th>
                                @for ($i = 1; $i <= 10; $i++)
                                    <th colspan="5">{{ $i }}</th>
                                @endfor
                            </tr>
                            <tr>
                                <th class="align-middle text-center">No</th>
                                <th class="align-middle text-center">Nama</th>
                                <th class="align-middle text-center">Jenis</th>
                                <th class="align-middle text-center">Sekolah</th>
                                <th class="align-middle text-center">Kelas</th>
                                @for ($i = 1; $i <= 10; $i++)
                                    <th>I</th>
                                    <th>II</th>
                                    <th>III</th>
                                    <th>IV</th>
                                    <th>Komentar {{ $i }}</th>
                                @endfor
                                <th class="align-middle text-center">Total Skor</th>
                                <th class="align-middle text-center">Kategori</th>
                            </tr>
                        </thead>
                        <tbody id="presentaseTableAdminBody" class="overflow-y-auto"></tbody>
                    </table>
                </div>
                <canvas id="hasilSiswaChart" class="p-5" width="150" height="100"></canvas>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

            var selectedJenis = '';
            var selectedKelas = '';

            function applyFilters() {
                table.column('jenis:name').search(selectedJenis);
                table.column('kelas:name').search(selectedKelas);
                table.draw();
            }

            var table = $('#presentaseTableAdmin').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('datatable.resultsAdmin') }}",
                    data: function (f) {
                        f.jenis = selectedJenis;
                        f.kelas = selectedKelas;
                    },
                },
                columns: [
                    { data: 'no', name: 'no' },
                    { data: 'nama', name: 'nama' },
                    { data: 'jenis', name: 'jenis'},
                    { data: 'sekolah', name: 'sekolah'},
                    { data: 'kelas', name: 'kelas' },
                    @for ($i = 1; $i <= 10; $i++)
                        { data: 'q{{ $i }}_I', name: 'q{{ $i }}_I' },
                        { data: 'q{{ $i }}_II', name: 'q{{ $i }}_II' },
                        { data: 'q{{ $i }}_III', name: 'q{{ $i }}_III' },
                        { data: 'q{{ $i }}_IV', name: 'q{{ $i }}_IV' },
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
                        text: '<i class="fas fa-file-excel"></i>&nbsp;Excel: Detail',
                        className: 'btn btn-sm btn-outline-success',
                        titleAttr: 'Ekspor ke Excel',
                        action: async function (e, dt, button, config) {
                            await loadScript('https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js');

                            const tableData = dt.buttons.exportData({
                                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14,
                                15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 
                                32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50,
                                51, 52, 53, 54, 55, 56]
                            });

                            const workbook = new ExcelJS.Workbook();
                            const worksheet = workbook.addWorksheet('Sheet1');

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
                            saveAs(new Blob([buffer]), 'Admin: Presentase_Hasil_Siswa.xlsx');
                        }
                    },
                    {
                        text: '<i class="fas fa-file-excel"></i>&nbsp;Excel: Rangkuman',
                        className: 'btn btn-sm btn-outline-success',
                        titleAttr: 'Ekspor ke Excel',
                        action: async function (e, dt, button, config) {
                            await loadScript('https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js');

                            const tableData = dt.buttons.exportData({
                                columns: [0, 1, 2, 3, 4, 55, 56]
                            });

                            const workbook = new ExcelJS.Workbook();
                            const worksheet = workbook.addWorksheet('Sheet1');

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
                            saveAs(new Blob([buffer]), 'Admin: Presentase_Hasil_Siswa.xlsx');
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="fas fa-file-pdf"></i>&nbsp;PDF: Detail',
                        className: 'btn btn-sm btn-outline-danger',
                        titleAttr: 'Ekspor ke PDF',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        margin: [10, 10, 10, 10],
                        filename: function() {
                            return 'Admin: Laporan_Hasil_Siswa';
                        },
                        customize: function (doc) {
                            const columnGroups = [
                                [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 55, 56],
                                [0, 1, 2, 3, 4, 10, 11, 12, 13, 14, 55, 56],
                                [0, 1, 2, 3, 4, 15, 16, 17, 18, 19, 55, 56],
                                [0, 1, 2, 3, 4, 20, 21, 22, 23, 24, 55, 56],
                                [0, 1, 2, 3, 4, 25, 26, 27, 28, 29, 55, 56],
                                [0, 1, 2, 3, 4, 30, 31, 32, 33, 34, 55, 56],
                                [0, 1, 2, 3, 4, 35, 36, 37, 38, 39, 55, 56],
                                [0, 1, 2, 3, 4, 40, 41, 42, 43, 44, 55, 56],
                                [0, 1, 2, 3, 4, 45, 46, 47, 48, 49, 55, 56],
                                [0, 1, 2, 3, 4, 50, 51, 52, 53, 54, 55, 56]
                            ];

                            let newContent = [];

                            columnGroups.forEach((group, index) => {
                                let pageContent = [];

                                doc.content.forEach(item => {
                                    if (item.table) {
                                        let tableClone = JSON.parse(JSON.stringify(item));

                                        tableClone.table.body = tableClone.table.body.map(row => 
                                            row.filter((_, cellIndex) => group.includes(cellIndex))
                                        );

                                        pageContent.push(tableClone);
                                    } else {
                                        pageContent.push(item); 
                                    }
                                });

                                if (index < columnGroups.length - 1) {
                                    pageContent.push({ text: '', pageBreak: 'after' });
                                }

                                newContent = newContent.concat(pageContent);
                            });

                            doc.content = newContent;

                            doc.styles.tableBodyEven = { alignment: 'center', fontSize: 11, lineHeight: 1.2 };
                            doc.styles.tableBodyOdd = { alignment: 'center', fontSize: 11, lineHeight: 1.2 };

                            doc.content[0].layout = {
                                hLineWidth: function(i) { return 0.5; },
                                vLineWidth: function(i) { return 0.5; },
                                hLineColor: function(i) { return '#aaa'; },
                                vLineColor: function(i) { return '#aaa'; },
                                paddingLeft: function(i) { return 4; },
                                paddingRight: function(i) { return 4; }
                            };

                            doc.content = doc.content.filter(item => {
                                return !(item.text && item.text.includes('4TA-Literasi&Numerasi'));
                            });
                            
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="fas fa-file-pdf"></i>&nbsp;PDF: Rangkuman',
                        className: 'btn btn-sm btn-outline-danger',
                        titleAttr: 'Ekspor ke PDF',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 55, 56]
                        },
                        filename: function() {
      
                            return 'Guru: Laporan_Hasil_Siswa';
                        
                        },
                        customize: function (doc) {

                            let newContent = [];

                            columnGroups.forEach((group, index) => {
                                let pageContent = [];

                                doc.content.forEach(item => {
                                    if (item.table) {
                                        let tableClone = JSON.parse(JSON.stringify(item));

                                        tableClone.table.body = tableClone.table.body.map(row => 
                                            row.filter((_, cellIndex) => group.includes(cellIndex))
                                        );

                                        pageContent.push(tableClone);
                                    } else {
                                        pageContent.push(item); 
                                    }
                                });

                                if (index < columnGroups.length - 1) {
                                    pageContent.push({ text: '', pageBreak: 'after' });
                                }

                                newContent = newContent.concat(pageContent);
                            });

                            doc.content = newContent;

                            doc.styles.tableBodyEven = { alignment: 'center', fontSize: 11, lineHeight: 1.2 };
                            doc.styles.tableBodyOdd = { alignment: 'center', fontSize: 11, lineHeight: 1.2 };

                            doc.content[0].layout = {
                                hLineWidth: function(i) { return 0.5; },
                                vLineWidth: function(i) { return 0.5; },
                                hLineColor: function(i) { return '#aaa'; },
                                vLineColor: function(i) { return '#aaa'; },
                                paddingLeft: function(i) { return 4; },
                                paddingRight: function(i) { return 4; }
                            };

                            doc.content = doc.content.filter(item => {
                                return !(item.text && item.text.includes('4TA-Literasi&Numerasi'));
                            });

                            doc.pageMargins = [10, 10, 10, 10];
                        }
                    },
                ],
                initComplete: function () {
                    $('div.dataTables_filter input').addClass('form-control');
                    $('div.dataTables_length').addClass('d-flex align-items-center');
                    $('div.dataTables_filter').addClass('d-flex justify-content-end align-items-center');

                    $('div.dataTables_length').append(`
                        <div class="ml-3 mb-2 p-2">
                            <label for="jenisSelect" class="form-label">Pilih Jenis:</label>
                            <select id="jenisSelect" class="p-2 form-select btn-warning rounded text-bold">
                                <option value="">Semua Jenis</option>
                                <option value="literasi">Literasi</option>
                                <option value="numerasi">Numerasi</option>
                            </select>
                        </div>
                        <div class="ml-3 mb-2 p-2">
                            <label for="kelasSelect" class="form-label">Pilih Kelas:</label>
                            <select id="kelasSelect" class="p-2 form-select btn-primary rounded text-bold">
                                <option value="">Semua Kelas</option>
                                @foreach($kelass as $kelas)
                                    <option value="{{ $kelas->id_kelas }}">{{ $kelas->id_kelas }} - {{ $kelas->nama_sekolah }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="ml-3 mb-2">
                            <button type="button" class="btn btn-success text-bold" id="applyFilter"><p class="text-bold mb-0">Terapkan Filter</p></button>
                        </div>
                    `);

                    $('#applyFilter').click(function() {
                        selectedJenis = $('#jenisSelect').val();
                        selectedKelas = $('#kelasSelect').val();
                        applyFilters();
                    });
                }
            });

            table.on('draw.dt', function() {
                var info = table.page.info();
                table.column(0, { search: 'applied', order: 'applied' }).nodes().each(function(cell, i) {
                    cell.innerHTML = i + 1 + info.start;
                });
            });

            table.on('draw', function() {
                var tableData = table.rows({ filter: 'applied' }).data();

                if (tableData.length > 0) {
                    var labels = [];
                    var data = [];
                    var categories = [];
                    var colors = [];
                    var kelas = [];

                    tableData.each(function(row) {
                        labels.push(row.nama);
                        data.push(parseInt(row.skor_total));
                        categories.push(row.kategori_skor);
                        colors.push(getRandomColor());
                        kelas.push(row.kelas);
                    });

                    updateChart(labels, data, categories, colors, kelas);
                } else {
                    clearChart();
                }
            });

            function updateChart(labels, data, categories, colors, kelas) {
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
                                            'Kategori: ' + categories[index],
                                        ];
                                    }
                                }
                            }
                        }
                    }
                });
            }

            function clearChart() {
                if (window.siswaChart) {
                    window.siswaChart.destroy();
                }
            }

            function getRandomColor() {
                var letters = '0123456789ABCDEF';
                var color = '#';
                for (var i = 0; i < 6; i++) {
                    color += letters[Math.floor(Math.random() * 16)];
                }
                return color;
            }
            
        });

    </script>

    @endsection
