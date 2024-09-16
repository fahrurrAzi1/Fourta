<?php

namespace App\Http\Controllers;

use App\Models\Soal;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\SkorJawaban;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SiswaController extends Controller
{

    public function hasilJawaban(Request $request)
    {
        // $kelasId = $request->input('kelas');

        $guruId = $request->session()->get('id_guru');

        $kelas = Kelas::where('guru_id', $guruId)->first();
    
        $kelass = Kelas::where('guru_id', $guruId)
                ->whereHas('siswadankelas') 
                ->get();
    
        $kelasId = $kelas ? $kelas->id : null;

        $results = SkorJawaban::with(['siswa', 'soal'])
                ->when($kelasId, function ($query) use ($kelasId) {
                    return $query->whereHas('siswa', function ($query) use ($kelasId) {
                        $query->where('id_kelas', $kelasId); 
                    });
                })
                ->join('kelass', 'skor_jawaban.kelas_id', '=', 'kelass.id')
                ->select('skor_jawaban.*', 'kelass.id as kelas_id') 
                ->get();

        foreach ($results as $hasil) {
            $hasil->kategori = $hasil->kosong ? 'numerasi' : 'literasi';
            $hasil->nomor_soal = $hasil->id_soal;
            $hasil->kelas = Kelas::find($hasil->kelas_id);
        }

        return view('siswa.hasil-jawaban', compact('kelass','results'));
    }

    public function getHasil()
    {
        
        $guruId = session('id_guru');

        $kelas = Kelas::where('guru_id', $guruId)->first();
        $kelasId = $kelas ? $kelas->id : null;

        $hasil = SkorJawaban::with(['siswa', 'komentar', 'soal'])
                ->when($kelasId, function ($query) use ($kelasId) {
                    return $query->where('kelas_id', $kelasId);
                })
                ->get();

        $groupedResults = $hasil->groupBy('id_siswa');

        $i = 1;

        return DataTables::of($groupedResults)
            ->addIndexColumn()
            ->addColumn('no', function($row) use (&$i) {
                return $i++; 
            })
            ->addColumn('nama', function($row) {

                return $row->first()->siswa->name; 

            })
            ->addColumn('jenis', function($row) {

                return $row->first()->jenis; 

            })
            ->addColumn('q1_komentar', function($row) {
                $firstItem = $row->where('nomor_soal', 1)->first();  
                if ($firstItem && $firstItem->komentar) {
                    return $firstItem->komentar->isi_komentar;  
                }
                return '';  
            })
            ->addColumn('q2_komentar', function($row) {
                $firstItem = $row->where('nomor_soal', 2)->first();  
                if ($firstItem && $firstItem->komentar) {
                    return $firstItem->komentar->isi_komentar;  
                }
                return '';  
            })
            ->addColumn('q3_komentar', function($row) {
                $firstItem = $row->where('nomor_soal', 3)->first();  
                if ($firstItem && $firstItem->komentar) {
                    return $firstItem->komentar->isi_komentar;  
                }
                return '';  
            })
            ->addColumn('q4_komentar', function($row) {
                $firstItem = $row->where('nomor_soal', 4)->first();  
                if ($firstItem && $firstItem->komentar) {
                    return $firstItem->komentar->isi_komentar;  
                }
                return '';  
            })
            ->addColumn('q5_komentar', function($row) {
                $firstItem = $row->where('nomor_soal', 5)->first();  
                if ($firstItem && $firstItem->komentar) {
                    return $firstItem->komentar->isi_komentar;  
                }
                return '';  
            })
            ->addColumn('q6_komentar', function($row) {
                $firstItem = $row->where('nomor_soal', 6)->first();  
                if ($firstItem && $firstItem->komentar) {
                    return $firstItem->komentar->isi_komentar;  
                }
                return '';  
            })
            ->addColumn('q7_komentar', function($row) {
                $firstItem = $row->where('nomor_soal', 7)->first();  
                if ($firstItem && $firstItem->komentar) {
                    return $firstItem->komentar->isi_komentar;  
                }
                return '';  
            })
            ->addColumn('q8_komentar', function($row) {
                $firstItem = $row->where('nomor_soal', 8)->first();  
                if ($firstItem && $firstItem->komentar) {
                    return $firstItem->komentar->isi_komentar;  
                }
                return '';  
            })
            ->addColumn('q9_komentar', function($row) {
                $firstItem = $row->where('nomor_soal', 9)->first();  
                if ($firstItem && $firstItem->komentar) {
                    return $firstItem->komentar->isi_komentar;  
                }
                return '';  
            })
            ->addColumn('q10_komentar', function($row) {
                $firstItem = $row->where('nomor_soal', 10)->first();  
                if ($firstItem && $firstItem->komentar) {
                    return $firstItem->komentar->isi_komentar;  
                }
                return '';  
            })
            ->addColumn('sekolah',function($row){
                return $row->first()->siswa->nama_sekolah;
            })
            ->addColumn('kelas', function ($row) {
                return optional($row->first()->kelas)->id_kelas;
            })
            ->addColumn('skor_total', function($row) {

                $totalSkor = 0;
                    
                for ($i = 1; $i <= 10; $i++) {

                    // definisikan untuk setiap skor
                    $skorJawabanSiswa = $row->where('id_soal', $i)->first()->skor_jawaban_siswa ?? 0;
                    $skorYakinJawaban = $row->where('id_soal', $i)->first()->skor_yakin_jawaban ?? 0;
                    $skorAlasan = $row->where('id_soal', $i)->first()->skor_alasan ?? 0;
                    $skorYakinAlasan = $row->where('id_soal', $i)->first()->skor_yakin_alasan ?? 0;

                    // persamaan untuk skor jawaban dan skor yakin jawaban
                    if ($skorJawabanSiswa == 0 && $skorYakinJawaban == 1) {
                            $skorYakinJawaban = -1;
                    }

                    // persamaan untuk skor alasan dan skor yakin alasan
                    if ($skorAlasan == 0 && $skorYakinAlasan == 1) {
                        $skorYakinAlasan = -1;
                    }

                    // hitung keseluruhan nilai nya
                    $totalSkor += $skorJawabanSiswa + $skorYakinJawaban + $skorAlasan + $skorYakinAlasan ;

                }

                // kembalikan nilai nya
                $skorAkhir = ($totalSkor / 40) * 100;

                // update skor_akhir ke database
                foreach ($row as $item) {
                    $item->update(['skor_akhir' => $skorAkhir]);
                }

                // kembalikan nilai nya
                return $skorAkhir;

            })
            ->addColumn('kategori_skor', function($row) {

                $totalSkor = 0;
                    
                for ($i = 1; $i <= 10; $i++) {

                    // definisikan untuk setiap skor
                    $skorJawabanSiswa = $row->where('id_soal', $i)->first()->skor_jawaban_siswa ?? 0;
                    $skorYakinJawaban = $row->where('id_soal', $i)->first()->skor_yakin_jawaban ?? 0;
                    $skorAlasan = $row->where('id_soal', $i)->first()->skor_alasan ?? 0;
                    $skorYakinAlasan = $row->where('id_soal', $i)->first()->skor_yakin_alasan ?? 0;

                    // persamaan untuk skor jawaban dan skor yakin jawaban
                    if ($skorJawabanSiswa == 0 && $skorYakinJawaban == 1) {
                            $skorYakinJawaban = -1;
                    }

                    // persamaan untuk skor alasan dan skor yakin alasan
                    if ($skorAlasan == 0 && $skorYakinAlasan == 1) {
                        $skorYakinAlasan = -1;
                    }

                    // hitung keseluruhan nilai nya
                    $totalSkor += $skorJawabanSiswa + $skorYakinJawaban + $skorAlasan + $skorYakinAlasan ;

                }

                $skorAkhir = ($totalSkor / 40) * 100;

                if ($skorAkhir <= 60) {
                    $kategoriSkor = 'Kurang Baik';
                } elseif ($skorAkhir <= 70) {
                    $kategoriSkor = 'Cukup Baik';
                } elseif ($skorAkhir <= 80) {
                    $kategoriSkor = 'Baik';
                } else {
                    $kategoriSkor = 'Sangat Baik';
                }
                
                // upadate kategori_skor ke database
                foreach ($row as $item) {
                    $item->update(['kategori_skor' => $kategoriSkor]);
                }

                // kembalikan nilai kategori_skor
                return $kategoriSkor;

            })
            ->make(true);
    }

    // alternatif kode diatas
        
    // $kelas = Kelas::where('guru_id', Auth::id())
    //           ->select('id', 'id_kelas', 'sekolah')
    //           ->get();

    // $results = SkorJawaban::with(['siswa', 'komentar', 'soal'])->get();

    // $filteredResults = $results->filter(function($result) use ($kelas) {
    //     return $kelas->contains('id', $result->siswa->kelas_id);
    // });

    // $groupedResults = $filteredResults->groupBy('id_siswa');
        

    public function jawabSoal(Request $request)
    {
        $guruId = session('id_guru');
        
        // $kelass = Kelas::all();

        $kelass = Kelas::where('guru_id', $guruId)
                ->whereHas('siswadankelas') 
                ->get();

        $jenis = $request->input('jenis', '');
        session(['jenis' => $jenis]);

        // $soals = Soal::when($jenis, function ($query, $jenis) {
        //     return $query->where('jenis', $jenis);
        // })->get();

        $soals = Soal::when($jenis, function ($query) use ($jenis, $kelass) {
            return $query->where('jenis', $jenis)
                         ->whereIn('kelas_id', $kelass->pluck('id'));
        })->get();

        return view('siswa.jawab-soal', compact('jenis', 'soals','kelass'));
    }

    public function selesaiJawab()
    {
        return view('siswa.selesai-jawab');
    }

    public function getStudentData($id_kelas)
    {
 
        $siswas = Siswa::with('kelass')->findOrFail($id_kelas);
        
        return response()->json([
            'nis' => $siswas->nis,
            'nama' => $siswas->name,
            'email' => $siswas->email,
            'sekolah' => $siswas->nama_sekolah,
            'kelas' => $siswas->kelass->id_kelas  
        ]);
    }
}
