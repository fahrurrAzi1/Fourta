<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Soal;
use App\Models\Hasil;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Sekolah;
use App\Models\BobotSoal;
use App\Models\SkorJawaban;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class GuruController extends Controller
{
    
    public function upload(Request $request)
    {
        $request->validate([
            'upload' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->file('upload')->isValid()) {
            $path = $request->file('upload')->store('uploads', 'public');
            $url = asset('storage/' . $path);

            return response()->json(['uploaded' => 1, 'url' => $url]);
        }

        return response()->json(['uploaded' => 0, 'error' => ['message' => 'File upload failed.']]);
    }
    
    public function store(Request $request)
    {

        $validatedData = $request->validate([
            'id_kelas' => 'required|string|max:255',
            'nama_sekolah' => 'required|string|max:255',
        ]);
    
        $kelas = Kelas::create([
            'id_kelas' => $validatedData['id_kelas'],
            'nama_sekolah' => $validatedData['nama_sekolah'],
            'guru_id' => Auth::guard('guru')->id(),
        ]);
    
        $sekolah = Sekolah::create([
            'nama_sekolah' => $kelas->nama_sekolah,
            'kelas_id' => $kelas->id, 
        ]);
    
        session(['kelas' => [
            'id_kelas' => $kelas->id_kelas,
            'nama_sekolah' => $kelas->nama_sekolah,
            'guru_id' => $kelas->guru_id,
        ]]);
    
        return redirect()->route('guru.kelola-kelas')->with('success', 'Kelas berhasil disimpan!');
    }

    public function destroy($id)
    {
        $kelas = Kelas::find($id);

        if (!$kelas) {
            return redirect()->back()->with('error', 'Kelas tidak ditemukan');
        }

        $kelas->delete();

        return redirect()->back()->with('success', 'Kelas berhasil dihapus');

    }
        
    public function inputSoal(Request $request)
    {
        $guruId = Auth::id(); 

        $kelass = Kelas::where('guru_id', $guruId)->get();

        $jenis = $request->input('jenis', 'literasi'); 
    
        $soals = Soal::where('jenis', $jenis)->get();
    
        return view('guru.input-soal', compact('soals', 'kelass'));
    }
    
    public function kelolaKelas(Request $request)
    {
        $guruId = Auth::id(); 

        $kelass = Kelas::where('guru_id', $guruId)->get();

        if ($request->ajax()) {
            $data = Kelas::with('guru')->where('guru_id', $guruId)->get();
            return Datatables::of($data)
                ->addColumn('aksi', function ($row) {
                    return '
                        <button class="btn btn-warning btn-sm edit-class font-weight-bold" data-id="' . $row->id . '" data-kelas="' . $row->id_kelas . '" data-sekolah="' . $row->nama_sekolah . '">Edit</button>
                        <button class="btn btn-danger btn-sm delete-class font-weight-bold" data-id="' . $row->id . '">Hapus</button>
                        <form id="delete-form-' . $row->id . '" action="' . url('/guru/kelas/hapus/' . $row->id) . '" method="POST" style="display: none;">
                            ' . csrf_field() . method_field('DELETE') . '
                        </form>
                    ';
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }

        return view('guru.kelola-kelas', compact('kelass'));
    }

    public function kelolaJawaban(Request $request)
    {
        $kelasId = $request->session()->get('kelas_id');

        // $kelass = Kelas::all();

        $siswas = Siswa::all();

        $guruId = Auth::id();

        $kelass = Kelas::where('guru_id', $guruId)->get();

        $idSiswa = $request->input('id_siswa');

        $jenis = $request->input('jenis');

        $hasil =  Hasil::whereHas('soal', function ($query) use ($jenis) {
                    $query->where('jenis', $jenis);
                })
                ->whereHas('siswa', function ($query) use ($idSiswa) {
                    $query->where('id_siswa', $idSiswa);
                })
                ->where('kelas_id', $kelasId)
                ->get();

        if ($request->ajax()) {
            return response()->json($hasil);
        }

        return view('guru.kelola-jawaban', compact('hasil','kelass','siswas'));
    }

    public function getSiswaByKelas(Request $request)
    {
        $id_kelas = $request->input('id_kelas');
        
        $siswas = DB::table('siswas')
                ->join('kelass', 'siswas.id_kelas', '=', 'kelass.id')  
                ->where('siswas.id_kelas', $id_kelas)  
                ->select('siswas.id_siswa', 'siswas.name', 'kelass.id_kelas')  
                ->get();
    
        return response()->json(['siswas' => $siswas]);
    }
    
    public function getGuruData($guru_id)
    {
 
        $gurus = Guru::with('kelas')->findOrFail($guru_id);

        $kelas = Kelas::where('guru_id', $guru_id)->first(); 
        
        return response()->json([
            'nip' => $gurus->nip,
            'nama' => $gurus->name,
            'email' => $gurus->email,
            'kelas' => $kelas ? $kelas->id_kelas : 'Kosong',
            'nama_sekolah' => $kelas ? $kelas->nama_sekolah : 'Kosong'
        ]);
    }

    // public function presentaseHasil(Request $request)
    // {
    //     return view('guru.presentase-hasil');
    // }

}
