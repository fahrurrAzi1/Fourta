<?php

namespace App\Http\Controllers\Auth;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Sekolah;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SiswaAuthController extends Controller
{   
    public function siswaDashboard()
    {
        $siswa = Auth::user();
        $kelas = Kelas::with('guru')->where('id', $siswa->id_kelas)->first();

        return view('dashboard', compact('kelas'));
    }
 
    public function showRegisterForm()
    {
        // $kelas = \App\Models\Kelas::all();
        $kelas = \App\Models\Kelas::with('guru')->get();
        return view('auth.siswa-register',compact('kelas'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nis' => 'required|string|regex:/^\d+$/|max:255|unique:siswas',
            'email' => 'required|string|email|max:255|unique:siswas',
            'password' => 'required|string|min:8|confirmed',
            'id_kelas' => 'required|exists:kelass,id',
        ]);

        $sekolah = Sekolah::where('kelas_id', $request->id_kelas)->first();

        if ($sekolah) {

            Siswa::create([
                'name' => $request->name,
                'nis' => $request->nis,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'siswa',
                'id_kelas' => $request->id_kelas,
                'nama_sekolah' => $sekolah->nama_sekolah,
            ]);    
        
            return redirect()->route('siswa.login')->with('success', 'Registrasi berhasil!');

        }
        
        return redirect()->back()->with('error', 'Sekolah tidak ditemukan untuk kelas yang dipilih.');

    }

    public function showLoginForm()
    {
        return view('auth.siswa-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (Auth::guard('siswa')->attempt($request->only('email', 'password'))) {

            $siswa = Auth::guard('siswa')->user();
            
            $request->session()->put('id_siswa', $siswa->id_siswa);

            $kelas = Kelas::where('id', $siswa->id_kelas)->first();

            if ($kelas) {
                $guru = Guru::where('id_guru', $kelas->guru_id)->first();

                if ($guru) {
                    $request->session()->put('kelas_id', $kelas->id);
                    $request->session()->put('id_kelas', $kelas->id_kelas);
                    $request->session()->put('nama_sekolah', $kelas->nama_sekolah);
                    $request->session()->put('id_guru', $guru->id_guru);
                }
                
            }

            return redirect()->route('siswa.dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('siswa')->logout();

        $request->session()->forget('id_siswa');
        $request->session()->forget('kelas_id');
        $request->session()->forget('id_kelas');
        $request->session()->forget('nama_sekolah');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
