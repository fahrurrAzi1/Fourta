<?php

namespace App\Http\Controllers\Auth;

use App\Models\Guru;
use App\Models\Kelas;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class GuruAuthController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.guru-register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nip' => 'required|string|regex:/^\d+$/|max:255|unique:gurus',
            'email' => 'required|string|email|max:255|unique:gurus',
            'password' => 'required|string|min:8|confirmed',
        ]);

        Guru::create([
            'name' => $request->name,
            'nip' => $request->nip,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'guru',
        ]);

        return redirect()->route('guru.login');
    }

    public function showLoginForm()
    {
        return view('auth.guru-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (Auth::guard('guru')->attempt($request->only('email', 'password'))) {

            $guru = Auth::guard('guru')->user();
            
            $request->session()->put('id_guru', $guru->id_guru);

            $kelas = Kelas::where('guru_id', $guru->id_guru)->first();

            if ($kelas) {
                $request->session()->put('kelas_id', $kelas->id);
                $request->session()->put('id_kelas', $kelas->id_kelas);
                $request->session()->put('nama_sekolah', $kelas->nama_sekolah);
            } else {
                $request->session()->forget(['kelas_id', 'id_kelas', 'nama_sekolah']);
            }

            return redirect()->route('guru.dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('guru')->logout();

        $request->session()->forget('id_guru');
        $request->session()->forget('kelas_id');
        $request->session()->forget('id_kelas');
        $request->session()->forget('nama_sekolah');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
    
}
