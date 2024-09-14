<?php

namespace App\Http\Controllers;

use App\Models\Soal;
use App\Models\Hasil;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Sekolah;
use App\Models\Komentar;
use App\Models\BobotSoal;
use App\Models\SkorJawaban;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class SoalController extends Controller
{
    public function soalStore(Request $request)
    { 
        $request->validate([
            'jenis' => 'required|string',
            'pertanyaan' => 'required|string',
            'kelas_id' => 'required|exists:kelass,id',
            'waktu' => 'required|integer|min:1',
        ]);

        // cek jumlah berdasarkan jenis soal
        $soalCount = Soal::where('jenis', $request->input('jenis'))->where('kelas_id', $request->input('kelas_id'))->count();

        if ($soalCount >= 10) {
            return redirect()->back()->with('error', 'Jumlah soal untuk jenis' 
            . ucfirst($request->input('jenis')) . 'sudah mencapai batas maksimum 10.');
        }
    
        $soal = new Soal();
        $soal->jenis = $request->input('jenis');
        $soal->pertanyaan = $request->input('pertanyaan');
        $soal->kelas_id = $request->input('kelas_id');
        $soal->waktu = $request->input('waktu');
        $soal->save();

        // simpan data pada tabel bobot soal
        $bobotSoalData = BobotSoal::where('id_soal', $soal->id)->first();

        if (!$bobotSoalData) {
            $bobotSoal = new BobotSoal();
            $bobotSoal->id_soal = $soal->id;
            $bobotSoal->skor_jawaban_siswa = '1';
            $bobotSoal->skor_yakin_jawaban = '1';
            $bobotSoal->skor_alasan = '1';
            $bobotSoal->skor_yakin_alasan = '1';
            $bobotSoal->save();
        }

        return redirect()->route('guru.input-soal')->with('success', 'Soal berhasil disimpan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'pertanyaan' => 'required|string',
            'waktu' => 'required|integer|min:1',
        ]);

        $soal = Soal::findOrFail($id);
        $soal->pertanyaan = $request->input('pertanyaan');
        $soal->waktu = $request->input('waktu');
        $soal->save();

        return redirect()->route('guru.input-soal')->with('success', 'Soal berhasil diperbarui!');
    }

    public function deactivate($id)
    {
        $soal = Soal::find($id);
        if ($soal) {
            $soal->status = 'off';
            $soal->save();
        }
        return redirect()->back()->with('success', 'Soal dinonaktifkan!');
    }

    public function delete($id)
    {
        $soal = Soal::find($id);
        if ($soal) {
            
            BobotSoal::where('id_soal', $soal->id)->delete();
    
            $imagePath = public_path('uploads/' . $soal->image); 

            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }

            $soal->delete();
        }

        if (Soal::count() === 0) {
            DB::statement('ALTER TABLE soal AUTO_INCREMENT = 1');
        }
    
        return redirect()->back()->with('success', 'Soal telah dihapus!');
    }

    // kode filter sebelum memasukan jawab soal

    public function filter(Request $request)
    {
        $kelass = Kelas::all();

        $jenis = $request->input('jenis', 'literasi'); 
        
        // $soals = Soal::where('jenis', $jenis)->get();

        $kelasId = $request->input('kelas_id'); 

        $soals = Soal::where('jenis', $jenis)
                 ->where('kelas_id', $kelasId) 
                 ->get();

        return view('guru.input-soal', compact('soals','kelass'));
    }

    // kode untuk filter siswa untuk menginput jawaban
 
    // public function filterSiswa(Request $request)
    // {
    //     $jenis = $request->input('jenis', 'literasi');

    //     session(['jenis' => $jenis]);

    //     $soals = Soal::where('jenis', $jenis)->get();

    //     $idKelas = $request->session()->get('kelas_id');

    //     $soals = Soal::where('jenis', $jenis)
    //                 ->where('kelas_id', $idKelas)
    //                 ->get();

    //     return view('siswa.jawab-soal', compact('soals'));
    // }

    public function filterSiswa(Request $request)
    {
        
        $kelass = Kelas::all();

        $jenis = $request->input('jenis', 'literasi');
        $kelas_id = $request->input('kelas_id');

        session(['jenis' => $jenis, 'kelas_id' => $kelas_id]);

        $soals = DB::table('soal')
            ->join('kelass', 'soal.kelas_id', '=', 'kelass.id')
            ->where('soal.jenis', $jenis)
            ->where('kelass.id', $kelas_id)
            ->select('soal.*')
            ->get();

        return view('siswa.jawab-soal', compact('soals','kelass'));
    }


    public function isiSoal(Request $request)
    {

        $jenis = $request->session()->get('jenis');

        // $soals = Soal::where('jenis', $jenis)->get();

        $idKelas = $request->session()->get('kelas_id');

        $soals = Soal::where('jenis', $jenis)
                    ->where('kelas_id', $idKelas)
                    ->get();

        return view('siswa.isi-soal', compact('soals'));
    }


    // kode untuk update kelola jawaban di halaman guru

    public function updateSkorJawaban(Request $request)
    {
        $validatedData = $request->validate([
            'id_siswa' => 'required|exists:siswas,id_siswa',
            'id_soal' => 'required|exists:soal,id',
            'type' => 'required|string',
            'value' => 'required|integer',
        ]);

        // mengambil id_kelas pada siswa
        $siswa = Siswa::find($validatedData['id_siswa']);
        $kelasId = $siswa->id_kelas;

        // mengambil jenis soal berdasarkan id 
        $soal = Soal::find($validatedData['id_soal']);
        $jenis = $soal->jenis;

        $skor = SkorJawaban::firstOrCreate(
            ['id_siswa' => $validatedData['id_siswa'], 'id_soal' => $validatedData['id_soal'],'jenis' => $jenis],
            [
                'skor_jawaban_siswa' => 0,
                'skor_yakin_jawaban' => 0,
                'skor_alasan' => 0,
                'skor_yakin_alasan' => 0,
                'skor_akhir' => 0, 
                'kategori_skor' => '',
                'kelas_id' => $kelasId,
            ]
        );

        switch ($validatedData['type']) {
            case 'skor_jawaban_siswa':
                $skor->skor_jawaban_siswa = $validatedData['value'];
                break;
            case 'skor_yakin_jawaban':
                $skor->skor_yakin_jawaban = $validatedData['value'];
                break;
            case 'skor_alasan':
                $skor->skor_alasan = $validatedData['value'];
                break;
            case 'skor_yakin_alasan':
                $skor->skor_yakin_alasan = $validatedData['value'];
                break;
        }

        $skor->save();

        return response()->json(['success' => 'Skor berhasil diperbarui!']);
    }

    public function getKomentar(Request $request)
    {
        $validatedData = $request->validate([
            'id_siswa' => 'required|exists:siswas,id_siswa',
            'id_soal' => 'required|exists:soal,id', 
        ]);

        $komentar = Komentar::where('id_siswa', $validatedData['id_siswa'])
                            ->where('id_soal', $validatedData['id_soal'])
                            ->first();

        if ($komentar) {
            return response()->json(['comments' => $komentar->isi_komentar]);
        } else {
            return response()->json(['comments' => 'Tidak ada komentar ditemukan.']);
        }
    }

    public function saveKomentar(Request $request)
    {
        $validatedData = $request->validate([
            'id_siswa' => 'required|exists:siswas,id_siswa',
            'id_soal' => 'required|exists:soal,id', 
            'komentar' => 'required|string|max:500',
        ]);

        $siswa = Siswa::find($validatedData['id_siswa']);
        $kelasId = $siswa->id_kelas;
        
        $komentar = Komentar::updateOrCreate(
            [
                'id_siswa' => $validatedData['id_siswa'],
                'id_soal' => $validatedData['id_soal'],
                'kelas_id' => $kelasId,
            ],
            ['isi_komentar' => $validatedData['komentar']]
        );
    
        $skor = SkorJawaban::firstOrCreate(
            [
                'id_siswa' => $validatedData['id_siswa'],
                'id_soal' => $validatedData['id_soal'],
            ],
            [
                'skor_jawaban_siswa' => 0,
                'skor_yakin_jawaban' => 0,
                'skor_alasan' => 0,
                'skor_yakin_alasan' => 0,
                'id_komentar' => $komentar->id, 
            ]
        );
    
        $skor->id_komentar = $komentar->id;
        $skor->save();
    
        return response()->json([
            'message' => 'Komentar berhasil disimpan!',
            'komentar' => $komentar->isi_komentar,
            'id_komentar' => $komentar->id,
        ]);
    }

    // kode untuk presentase hasil dari siswa

    public function skorHasil(Request $request)
    {
        
        $guruId = Auth::id();

        $kelass = Kelas::where('guru_id', $guruId)->get();
    
        $kelasId = $request->input('kelas');

        $results = SkorJawaban::with(['siswa', 'soal'])
            ->when($kelasId, function ($query) use ($kelasId) {
                return $query->whereHas('siswa', function ($query) use ($kelasId) {
                    $query->where('id_kelas', $kelasId); 
                });
            })
            ->join('kelass', 'skor_jawaban.kelas_id', '=', 'kelass.id')
            ->select('skor_jawaban.*', 'kelass.id as kelas_id') 
            ->get();

        foreach ($results as $result) {
            $result->kategori = $result->kosong ? 'numerasi' : 'literasi';
            $result->nomor_soal = $result->id_soal;
            $result->kelas = Kelas::find($result->id_kelas);
        }
    
        return view('guru.presentase-hasil', compact('results','kelass'));

    }

    public function getResults()
    {
        
        // $results = SkorJawaban::with(['siswa', 'komentar', 'soal'])->get();

        $guru = Auth::guard('guru')->user();

        $results = SkorJawaban::with(['siswa', 'komentar', 'soal'])
                    ->whereHas('siswa.kelass', function($query) use ($guru) {
                        $query->where('guru_id', $guru->id_guru);
                    })
                    ->get();

        $groupedResults = $results->groupBy('id_siswa');

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
             ->addColumn('kelas', function ($row) {
                return optional($row->first()->kelas)->id_kelas;
            })
            ->addColumn('sekolah',function($row){
                return $row->first()->siswa->nama_sekolah;
            })
            ->addColumn('q1_I', function($row) { return $row->where('id_soal', 1)->first()->skor_jawaban_siswa ?? ''; })
            ->addColumn('q1_II', function($row) { return $row->where('id_soal', 1)->first()->skor_yakin_jawaban ?? ''; })
            ->addColumn('q1_III', function($row) { return $row->where('id_soal', 1)->first()->skor_alasan ?? ''; })
            ->addColumn('q1_IV', function($row) { return $row->where('id_soal', 1)->first()->skor_yakin_alasan ?? ''; })
            ->addColumn('q1_komentar', function($row) {
                $firstItem = $row->where('id_soal', 1)->first();  
                if ($firstItem && $firstItem->komentar) {
                    return $firstItem->komentar->isi_komentar;  
                }
                return '';  
            })            
            ->addColumn('q2_I', function($row) { return $row->where('id_soal', 2)->first()->skor_jawaban_siswa ?? ''; })
            ->addColumn('q2_II', function($row) { return $row->where('id_soal', 2)->first()->skor_yakin_jawaban ?? ''; })
            ->addColumn('q2_III', function($row) { return $row->where('id_soal', 2)->first()->skor_alasan ?? ''; })
            ->addColumn('q2_IV', function($row) { return $row->where('id_soal', 2)->first()->skor_yakin_alasan ?? ''; })
            ->addColumn('q2_komentar', function($row) {
                $firstItem = $row->where('id_soal', 2)->first();  
                if ($firstItem && $firstItem->komentar) {
                    return $firstItem->komentar->isi_komentar;  
                }
                return '';  
            })   
            ->addColumn('q3_I', function($row) { return $row->where('id_soal', 3)->first()->skor_jawaban_siswa ?? ''; })
            ->addColumn('q3_II', function($row) { return $row->where('id_soal', 3)->first()->skor_yakin_jawaban ?? ''; })
            ->addColumn('q3_III', function($row) { return $row->where('id_soal', 3)->first()->skor_alasan ?? ''; })
            ->addColumn('q3_IV', function($row) { return $row->where('id_soal', 3)->first()->skor_yakin_alasan ?? ''; })
            ->addColumn('q3_komentar', function($row) {
                $firstItem = $row->where('id_soal', 3)->first();  
                if ($firstItem && $firstItem->komentar) {
                    return $firstItem->komentar->isi_komentar;  
                }
                return '';  
            })
            ->addColumn('q4_I', function($row) { return $row->where('id_soal', 4)->first()->skor_jawaban_siswa ?? ''; })
            ->addColumn('q4_II', function($row) { return $row->where('id_soal', 4)->first()->skor_yakin_jawaban ?? ''; })
            ->addColumn('q4_III', function($row) { return $row->where('id_soal', 4)->first()->skor_alasan ?? ''; })
            ->addColumn('q4_IV', function($row) { return $row->where('id_soal', 4)->first()->skor_yakin_alasan ?? ''; })
            ->addColumn('q4_komentar', function($row) {
                $firstItem = $row->where('id_soal', 4)->first();  
                if ($firstItem && $firstItem->komentar) {
                    return $firstItem->komentar->isi_komentar;  
                }
                return '';  
            })
            ->addColumn('q5_I', function($row) { return $row->where('id_soal', 5)->first()->skor_jawaban_siswa ?? ''; })
            ->addColumn('q5_II', function($row) { return $row->where('id_soal', 5)->first()->skor_yakin_jawaban ?? ''; })
            ->addColumn('q5_III', function($row) { return $row->where('id_soal', 5)->first()->skor_alasan ?? ''; })
            ->addColumn('q5_IV', function($row) { return $row->where('id_soal', 5)->first()->skor_yakin_alasan ?? ''; })
            ->addColumn('q5_komentar', function($row) {
                $firstItem = $row->where('id_soal', 5)->first();  
                if ($firstItem && $firstItem->komentar) {
                    return $firstItem->komentar->isi_komentar;  
                }
                return '';  
            })
            ->addColumn('q6_I', function($row) { return $row->where('id_soal', 6)->first()->skor_jawaban_siswa ?? ''; })
            ->addColumn('q6_II', function($row) { return $row->where('id_soal', 6)->first()->skor_yakin_jawaban ?? ''; })
            ->addColumn('q6_III', function($row) { return $row->where('id_soal', 6)->first()->skor_alasan ?? ''; })
            ->addColumn('q6_IV', function($row) { return $row->where('id_soal', 6)->first()->skor_yakin_alasan ?? ''; })
            ->addColumn('q6_komentar', function($row) {
                $firstItem = $row->where('id_soal', 6)->first();  
                if ($firstItem && $firstItem->komentar) {
                    return $firstItem->komentar->isi_komentar;  
                }
                return '';  
            })
            ->addColumn('q7_I', function($row) { return $row->where('id_soal', 7)->first()->skor_jawaban_siswa ?? ''; })
            ->addColumn('q7_II', function($row) { return $row->where('id_soal', 7)->first()->skor_yakin_jawaban ?? ''; })
            ->addColumn('q7_III', function($row) { return $row->where('id_soal', 7)->first()->skor_alasan ?? ''; })
            ->addColumn('q7_IV', function($row) { return $row->where('id_soal', 7)->first()->skor_yakin_alasan ?? ''; })
            ->addColumn('q7_komentar', function($row) {
                $firstItem = $row->where('id_soal', 7)->first();  
                if ($firstItem && $firstItem->komentar) {
                    return $firstItem->komentar->isi_komentar;  
                }
                return '';  
            })
            ->addColumn('q8_I', function($row) { return $row->where('id_soal', 8)->first()->skor_jawaban_siswa ?? ''; })
            ->addColumn('q8_II', function($row) { return $row->where('id_soal', 8)->first()->skor_yakin_jawaban ?? ''; })
            ->addColumn('q8_III', function($row) { return $row->where('id_soal', 8)->first()->skor_alasan ?? ''; })
            ->addColumn('q8_IV', function($row) { return $row->where('id_soal', 8)->first()->skor_yakin_alasan ?? ''; })
            ->addColumn('q8_komentar', function($row) {
                $firstItem = $row->where('id_soal', 8)->first();  
                if ($firstItem && $firstItem->komentar) {
                    return $firstItem->komentar->isi_komentar;  
                }
                return '';  
            })
            ->addColumn('q9_I', function($row) { return $row->where('id_soal', 9)->first()->skor_jawaban_siswa ?? ''; })
            ->addColumn('q9_II', function($row) { return $row->where('id_soal', 9)->first()->skor_yakin_jawaban ?? ''; })
            ->addColumn('q9_III', function($row) { return $row->where('id_soal', 9)->first()->skor_alasan ?? ''; })
            ->addColumn('q9_IV', function($row) { return $row->where('id_soal', 9)->first()->skor_yakin_alasan ?? ''; })
            ->addColumn('q9_komentar', function($row) {
                $firstItem = $row->where('id_soal', 9)->first();  
                if ($firstItem && $firstItem->komentar) {
                    return $firstItem->komentar->isi_komentar;  
                }
                return '';  
            })
            ->addColumn('q10_I', function($row) { return $row->where('id_soal', 10)->first()->skor_jawaban_siswa ?? ''; })
            ->addColumn('q10_II', function($row) { return $row->where('id_soal', 10)->first()->skor_yakin_jawaban ?? ''; })
            ->addColumn('q10_III', function($row) { return $row->where('id_soal', 10)->first()->skor_alasan ?? ''; })
            ->addColumn('q10_IV', function($row) { return $row->where('id_soal', 10)->first()->skor_yakin_alasan ?? ''; })
            ->addColumn('q10_komentar', function($row) {
                $firstItem = $row->where('id_soal', 10)->first();  
                if ($firstItem && $firstItem->komentar) {
                    return $firstItem->komentar->isi_komentar;  
                }
                return '';  
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


}

        // alternatif kode
        
        // $kelas = Kelas::where('guru_id', Auth::id())
        //           ->select('id', 'id_kelas', 'sekolah')
        //           ->get();

        // $results = SkorJawaban::with(['siswa', 'komentar', 'soal'])->get();

        // $filteredResults = $results->filter(function($result) use ($kelas) {
        //     return $kelas->contains('id', $result->siswa->kelas_id);
        // });

        // $groupedResults = $filteredResults->groupBy('id_siswa');

        // alternatif kode
        // $soalBelumDijawab = $soals->filter(function ($soalItem) {
        //     return DB::table('hasil')->where('id_soal', $soalItem->id)->whereNotNull('jawaban_siswa')->exists();
        // });

        // if (!$soalBelumDijawab->isEmpty()) {
        //     return redirect()->route('siswa.selesai-jawab');
        // } else {
        //     return view('siswa.jawab-soal', ['soals' => $soalBelumDijawab]);
        // }
        //akhir

        // public function filterSiswa(Request $request)
        // {
        //     $jenis = $request->input('jenis', 'literasi');
        //     session(['jenis' => $jenis]);  
        //     $soal = Soal::where('jenis', $jenis)->get();
        //     return view('siswa.jawab-soal', compact('soal'));
        // }