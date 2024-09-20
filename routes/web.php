<?php

use App\Models\Guru;
use App\Models\Soal;
use App\Models\Admin;
use App\Models\Kelas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\SoalController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\JawabanController;
use App\Http\Controllers\KomentarController;
use App\Http\Controllers\SkorJawabanController;
use App\Http\Controllers\Auth\GuruAuthController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\SiswaAuthController;
use App\Http\Controllers\SoalControllerAdmin;

Route::get('/', function () {
    return view('welcome');
});

// rute admin
Route::get('admin/register', [AdminAuthController::class, 'showRegisterForm'])->name('admin.register');
Route::post('admin/register', [AdminAuthController::class, 'register']);
Route::get('admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('admin/login', [AdminAuthController::class, 'login']);
Route::post('admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
Route::middleware(['auth:admin'])->group(function () {
    Route::get('admin/dashboard', function () {
        return view('admin.admin-dashboard');
    })->name('admin.dashboard');
    Route::get('admin/input-soal-admin', [AdminController::class, 'inputSoal'])->name('admin.input-soal-admin');
    Route::get('admin/kelola-kelas-admin', [AdminController::class, 'kelolaKelas'])->name('admin.kelola-kelas-admin');
    Route::post('admin/kelas/tambah', [AdminController::class, 'store']);
    Route::delete('/admin/kelas/hapus/{id}', [AdminController::class, 'destroy']);
    //rute kelola jawaban
    Route::get('admin/kelola-jawaban-admin',[AdminController::class, 'kelolaJawaban'])->name('admin.kelola-jawaban-admin');
    Route::post('admin/kelola-jawaban-admin', [AdminController::class, 'kelolaJawaban'])->name('admin.kelola-jawaban-admin.post');
    //rute presentase hasil siswa
    // Route::get('guru/presentase-hasil',[AdminController::class, 'presentaseHasil'])->name('guru.presentase-hasil');
    // untuk rute soal
    Route::post('/soal-admin', [SoalControllerAdmin::class, 'soalStore'])->name('soal-admin.store');
    Route::post('/soal-admin/deactivate/{id}', [SoalControllerAdmin::class, 'deactivate'])->name('soal-admin.deactivate');
    Route::delete('/soal-admin/{id}', [SoalControllerAdmin::class, 'delete'])->name('soal-admin.delete');
    Route::get('/soal-admin/filter', [SoalControllerAdmin::class, 'filter'])->name('soal-admin.filter');
    Route::get('/soal-admin/edit/{id}', [SoalControllerAdmin::class, 'edit'])->name('soal-admin.edit');
    // Route::post('/soal/update/{id}', [SoalControllerAdmin::class, 'update'])->name('soal.update');
    Route::put('/soal-admin/{id}', [SoalControllerAdmin::class, 'update'])->name('soal-admin.update');
    // untuk rute upload
    Route::post('/upload', [AdminController::class, 'upload'])->name('upload');
    //untuk mengirim komentar
    Route::get('/get-komentar', [SoalControllerAdmin::class, 'getKomentar']);
    // untuk mengambil komentar
    Route::post('/save-komentar', [SoalControllerAdmin::class, 'saveKomentar']);
    //rute untuk mengirim benar atau salah jawaban
    Route::post('/admin/update-skor-jawaban-admin', [SoalControllerAdmin::class, 'updateSkorJawaban'])->name('admin.update-skor-jawaban-admin');
    // rute untuk presentase hasil
    Route::get('/admin/presentase-hasil-admin', [SoalControllerAdmin::class, 'skorHasil'])->name('admin.presentase-hasil-admin');
    // rute untuk data-table presentase hasil
    Route::get('datatable/resultsAdmin', [SoalControllerAdmin::class, 'getResults'])->name('datatable.resultsAdmin');
    // mengambil data siswa di kelas
    Route::get('/admin/get-siswa-by-kelas', [AdminController::class, 'getSiswaByKelas'])->name('admin.get-siswa-by-kelas');
    // mengambil data untuk ekspor
    Route::get('/api/get-admin-data/{id}', [AdminController::class, 'getAdminData']);
});


// rute guru
Route::get('guru/register', [GuruAuthController::class, 'showRegisterForm'])->name('guru.register');
Route::post('guru/register', [GuruAuthController::class, 'register']);
Route::get('guru/login', [GuruAuthController::class, 'showLoginForm'])->name('guru.login');
Route::post('guru/login', [GuruAuthController::class, 'login']);
Route::post('guru/logout', [GuruAuthController::class, 'logout'])->name('guru.logout');
Route::middleware(['auth:guru'])->group(function () {
    //rute utama guru
    Route::get('guru/dashboard', function () {
        return view('guru.guru-dashboard');
    })->name('guru.dashboard');
    Route::get('guru/input-soal', [GuruController::class, 'inputSoal'])->name('guru.input-soal');
    Route::get('guru/kelola-kelas', [GuruController::class, 'kelolaKelas'])->name('guru.kelola-kelas');
    Route::post('guru/kelas/tambah', [GuruController::class, 'store']);
    Route::delete('/guru/kelas/hapus/{id}', [GuruController::class, 'destroy']);
    //rute kelola jawaban
    Route::get('guru/kelola-jawaban',[GuruController::class, 'kelolaJawaban'])->name('guru.kelola-jawaban');
    Route::post('guru/kelola-jawaban', [GuruController::class, 'kelolaJawaban'])->name('guru.kelola-jawaban.post');
    //rute presentase hasil siswa
    // Route::get('guru/presentase-hasil',[GuruController::class, 'presentaseHasil'])->name('guru.presentase-hasil');
    // untuk rute soal
    Route::post('/soal', [SoalController::class, 'soalStore'])->name('soal.store');
    Route::post('/soal/deactivate/{id}', [SoalController::class, 'deactivate'])->name('soal.deactivate');
    Route::delete('/soal/{id}', [SoalController::class, 'delete'])->name('soal.delete');
    Route::get('/soal/filter', [SoalController::class, 'filter'])->name('soal.filter');
    Route::get('/soal/edit/{id}', [SoalController::class, 'edit'])->name('soal.edit');
    // Route::post('/soal/update/{id}', [SoalController::class, 'update'])->name('soal.update');
    Route::put('/soal/{id}', [SoalController::class, 'update'])->name('soal.update');
    // untuk rute upload
    Route::post('/upload', [GuruController::class, 'upload'])->name('upload');
    //untuk mengirim komentar
    Route::get('/get-komentar', [SoalController::class, 'getKomentar']);
    // untuk mengambil komentar
    Route::post('/save-komentar', [SoalController::class, 'saveKomentar']);
    //rute untuk mengirim benar atau salah jawaban
    Route::post('/guru/update-skor-jawaban', [SoalController::class, 'updateSkorJawaban'])->name('guru.update-skor-jawaban');
    // rute untuk presentase hasil
    Route::get('/guru/presentase-hasil', [SoalController::class, 'skorHasil'])->name('guru.presentase-hasil');
    // rute untuk data-table presentase hasil
    Route::get('datatable/results', [SoalController::class, 'getResults'])->name('datatable.results');
    // mengambil data siswa di kelas
    Route::get('/guru/get-siswa-by-kelas', [GuruController::class, 'getSiswaByKelas'])->name('guru.get-siswa-by-kelas');
    // mengambil data untuk ekspor
    Route::get('/api/get-guru-data/{id}', [GuruController::class, 'getGuruData']);
});

// rute siswa
Route::get('siswa/register', [SiswaAuthController::class, 'showRegisterForm'])->name('siswa.register');
Route::post('siswa/register', [SiswaAuthController::class, 'register']);
Route::get('siswa/login', [SiswaAuthController::class, 'showLoginForm'])->name('siswa.login');
Route::post('siswa/login', [SiswaAuthController::class, 'login']);
Route::post('siswa/logout', [SiswaAuthController::class, 'logout'])->name('siswa.logout');
Route::middleware(['auth:siswa'])->group(function () {
Route::get('siswa/dashboard', function () {
    $siswa = Auth::user();
    $kelas = Kelas::with('guru')->where('id', $siswa->id_kelas)->first();
    return view('siswa.siswa-dashboard', compact('kelas'));
})->name('siswa.dashboard');
Route::get('siswa/hasil-jawaban', [SiswaController::class, 'hasilJawaban'])->name('siswa.hasil-jawaban');
Route::get('/siswa/filter', [SoalController::class, 'filterSiswa'])->name('siswa.filter');
Route::get('siswa/jawab-soal', [SiswaController::class, 'jawabSoal'])->name('siswa.jawab-soal');
Route::get('siswa/isi-soal', [SoalController::class, 'isiSoal'])->name('siswa.isi-soal');
// Route::post('/hasil', [JawabanController::class, 'store'])->name('jawaban.store');
Route::post('/jawaban/submit', [JawabanController::class, 'submit'])->name('jawaban.submit');
Route::get('siswa/selesai-jawab', [SiswaController::class, 'selesaiJawab'])->name('siswa.selesai-jawab');
// rute untuk data-table presentase hasil
Route::get('datatable/hasil', [SiswaController::class, 'getHasil'])->name('datatable.hasil');
// untuk waktu soal jika waktu habis
Route::post('/jawaban/get-next-soal', [JawabanController::class, 'getNextSoal'])->name('jawaban.getNextSoal');
// mengambil data untuk ekspor
Route::get('/api/get-student-data/{id}', [SiswaController::class, 'getStudentData']);
});

// rute umum untuk logout
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');

// rute default auth
Auth::routes();

Route::post('/ckeditor/upload', [App\Http\Controllers\CKEditorController::class, 'upload'])->name('ckeditor.upload');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
