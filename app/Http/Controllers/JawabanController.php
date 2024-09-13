<?php


namespace App\Http\Controllers;

use App\Models\Soal;
use App\Models\Hasil;
use Illuminate\Http\Request;

class JawabanController extends Controller
{
    public function submit(Request $request)
    {
        $data = $request->validate([
            'id_soal' => 'required|exists:soal,id',
            'jawaban_siswa' => 'required',
            'status_jawaban' => 'required|in:yakin,tidak',
            'alasan_siswa' => 'required',
            'status_alasan' => 'required|in:yakin,tidak',
            'current_soal_index' => 'required|integer',
            'kelas_id' => 'required|exists:soal,kelas_id',
            'id_siswa' => 'required|exists:siswas,id_siswa', 
        ]);

        Hasil::updateOrCreate(
            [
                'id_soal' => $data['id_soal'],
                'id_siswa' => $request->input('id_siswa'),
            ],
            [
                'kelas_id' => $data['kelas_id'],
                'jawaban_siswa' => $data['jawaban_siswa'],
                'status_jawaban' => $data['status_jawaban'],
                'alasan_siswa' => $data['alasan_siswa'],
                'status_alasan' => $data['status_alasan'],
            ]
        );

        $nextSoal = Soal::where('jenis', session('jenis'))
            ->where('id', '>', $data['id_soal'])
            ->first();

        if ($nextSoal) {
            return response()->json(['nextSoal' => $nextSoal]);
        } else {
            return response()->json(['finished' => true]);
        }
    }
    
    public function getNextSoal(Request $request)
    {
        $currentSoalIndex = $request->input('current_soal_index', 0);
        $jenis = session('jenis');

        $nextSoal = Soal::where('jenis', $jenis)
            ->where('id', '>', $request->input('id_soal'))
            ->first();

        if ($nextSoal) {
            return response()->json(['nextSoal' => $nextSoal, 'currentSoalIndex' => $currentSoalIndex + 1]);
        } else {
            return response()->json(['finished' => true]);
        }
    }


}

// alternatif kode
// public function submit(Request $request)
// {
//     $data = $request->validate([
//         'id_soal' => 'required|exists:soal,id',
//         'jawaban_siswa' => 'required',
//         'status_jawaban' => 'required|in:yakin,tidak',
//         'alasan_siswa' => 'required',
//         'status_alasan' => 'required|in:yakin,tidak',
//         'current_soal_index' => 'required|integer',
//         'kelas_id' => 'required|exists:soal,kelas_id',
//     ]);

//     Hasil::create([
//         'id_soal' => $data['id_soal'],
//         'id_siswa' => $request->input('id_siswa'),
//         'kelas_id' => $data['kelas_id'],
//         'jawaban_siswa' => $data['jawaban_siswa'],
//         'status_jawaban' => $data['status_jawaban'],
//         'alasan_siswa' => $data['alasan_siswa'],
//         'status_alasan' => $data['status_alasan'],
//     ]);

//     $nextSoal = Soal::where('jenis', session('jenis'))
//         ->where('id', '>', $data['id_soal'])
//         ->first();

//     if ($nextSoal) {
//         return response()->json(['nextSoal' => $nextSoal]);
//     } else {
//         return response()->json(['finished' => true]);
//     }
// }