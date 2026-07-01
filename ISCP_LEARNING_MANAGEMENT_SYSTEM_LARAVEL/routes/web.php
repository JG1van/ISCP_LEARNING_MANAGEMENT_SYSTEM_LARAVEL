<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LoginAllController;

//   ADMIN CONTROLLERS
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminProfilController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\MapelController;
use App\Http\Controllers\Admin\LessonController;
use App\Http\Controllers\Admin\ThemeController;
use App\Http\Controllers\Admin\CompetenceController;
use App\Http\Controllers\Admin\ExerciseController;
use App\Http\Controllers\Admin\ExerciseTypeController;
use App\Http\Controllers\Admin\ExerciseModelController;
use App\Http\Controllers\Admin\ExerciseItemController;
use App\Http\Controllers\Admin\SerialController;
use App\Http\Controllers\Admin\ClassroomController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\CSAdminController;
use App\Http\Controllers\CSController;
use App\Http\Controllers\Admin\QuestionCategoryController;
use App\Http\Controllers\Admin\ImportMateriController;
use App\Http\Controllers\Admin\DashboardController;
use Kreait\Firebase\Factory;



//  ROUTE AWAL
Route::get('/', fn() => view('welcome'));


Route::get('/login', [App\Http\Controllers\Auth\LoginAllController::class, 'showLogin'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\LoginAllController::class, 'login'])->name('login.process');
Route::post('/logout', [App\Http\Controllers\Auth\LoginAllController::class, 'logout'])->name('logout');


//  ROUTE DENGAN AUTHENTICATION + PREFIX ADMIN
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    //  DASHBOARD
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    //  PRODUK
    Route::prefix('produk')->name('produk.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/create', [ProductController::class, 'create'])->name('create');
        Route::post('/store', [ProductController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [ProductController::class, 'edit'])->name('edit');
        Route::put('/{id}/update', [ProductController::class, 'update'])->name('update');
        Route::delete('/{id}/destroy', [ProductController::class, 'destroy'])->name('destroy');
    });

    //  MAPEL
    Route::resource('mapel', MapelController::class);

    //  PELAJARAN
    Route::resource('pelajaran', LessonController::class);

    // 🏋️ PRA LATIHAN (TIPE & MODEL)
    Route::prefix('pra-soal')->name('pra-soal.')->group(function () {
        Route::resource('tipe', ExerciseTypeController::class);
        Route::resource('model', ExerciseModelController::class);
    });

    //  MATERI (TEMA, SUBTEMA, ITEM)
    Route::prefix('pelajaran/{lesson_id}/materi')->name('pelajaran.')->group(function () {
        Route::get('/', [ThemeController::class, 'index'])->name('materi.index');

        // Store
        Route::post('/theme', [ThemeController::class, 'storeTheme'])->name('theme.store');
        Route::post('/subtheme', [ThemeController::class, 'storeSubtheme'])->name('subtheme.store');
        Route::post('/item', [ThemeController::class, 'storeLessonItem'])->name('item.store');

        // Update
        Route::put('/theme/{id}', [ThemeController::class, 'updateTheme'])->name('theme.update');
        Route::put('/subtheme/{id}', [ThemeController::class, 'updateSubtheme'])->name('subtheme.update');
        Route::put('/item/{id}', [ThemeController::class, 'updateLessonItem'])->name('item.update');

        // Delete
        Route::delete('/theme/{id}', [ThemeController::class, 'destroyTheme'])->name('theme.destroy');
        Route::delete('/subtheme/{id}', [ThemeController::class, 'destroySubtheme'])->name('subtheme.destroy');
        Route::delete('/item/{id}', [ThemeController::class, 'destroyLessonItem'])->name('item.destroy');

        // Edit
        Route::get('/theme/{id}/edit', [ThemeController::class, 'editTheme'])->name('theme.edit');
        Route::get('/subtheme/{id}/edit', [ThemeController::class, 'editSubtheme'])->name('subtheme.edit');
        Route::get('/item/{id}/edit', [ThemeController::class, 'editLessonItem'])->name('item.edit');

        // Import
        Route::post('/import', [ImportMateriController::class, 'store'])->name('import');
    });

    //  KOMPETENSI DASAR (KD)
    Route::prefix('pelajaran/{lesson_id}/kd')->name('pelajaran.kd.')->group(function () {
        Route::get('/', [CompetenceController::class, 'index'])->name('index');
        Route::post('/', [CompetenceController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [CompetenceController::class, 'edit'])->name('edit');
        Route::put('/{id}', [CompetenceController::class, 'update'])->name('update');
        Route::delete('/{id}', [CompetenceController::class, 'destroy'])->name('destroy');
    });

    // ==========================
    // LATIHAN SOAL + SOAL DI DALAM LATIHAN
    // ==========================
    Route::prefix('pelajaran/{lesson_id}/judul_soal')->name('pelajaran.judul_soal.')->group(function () {

        //  Daftar Soal
        Route::get('/', [ExerciseController::class, 'index'])->name('index');
        Route::get('/create', [ExerciseController::class, 'create'])->name('create');
        Route::post('/', [ExerciseController::class, 'store'])->name('store');
        Route::get('/{exercise_id}/edit', [ExerciseController::class, 'edit'])->name('edit');
        Route::put('/{exercise_id}', [ExerciseController::class, 'update'])->name('update');
        Route::delete('/{exercise_id}', [ExerciseController::class, 'destroy'])->name('destroy');

        //  Soal di dalam Soal
        Route::prefix('{exercise_id}/soal')->name('soal.')->group(function () {
            Route::get('/', [ExerciseItemController::class, 'index'])->name('index');
            Route::get('/create', [ExerciseItemController::class, 'create'])->name('create');
            Route::post('/', [ExerciseItemController::class, 'store'])->name('store');
            Route::get('/{item_id}/edit', [ExerciseItemController::class, 'edit'])->name('edit');
            Route::put('/{item_id}', [ExerciseItemController::class, 'update'])->name('update');
            Route::delete('/{item_id}', [ExerciseItemController::class, 'destroy'])->name('destroy');
        });
    });

    //  SERIAL
    Route::get('/serial/riwayat', [SerialController::class, 'riwayat'])
        ->name('serial.riwayat');
    Route::resource('serial', SerialController::class);
    Route::post('/serial/{id}/extend', [SerialController::class, 'extend'])->name('serial.extend');
    Route::post('/serial/kirim-email', [SerialController::class, 'sendSerialEmail'])
        ->name('serial.kirim_email');

    //  KELAS
    Route::resource('kelas', ClassroomController::class);

    //  GURU
    Route::resource('guru', TeacherController::class);
    Route::post('/guru/{id}/reset-password', [TeacherController::class, 'resetPassword'])->name('guru.reset-password');

    //  SISWA
    Route::resource('siswa', StudentController::class);
    Route::post('/siswa/{id}/reset-password', [StudentController::class, 'resetPassword'])->name('siswa.reset-password');

    //  ADMIN
    Route::resource('admin', AdminController::class);
    Route::post('/admin/{id}/reset-password', [AdminController::class, 'resetPassword'])->name('data-admin.reset-password');

    //  PENGATURAN SISTEM
    Route::prefix('pengaturan')->name('pengaturan.')->group(function () {
        Route::get('/', fn() => view('admin.pengaturan.index'))->name('index');
    });

    // 🧑 PROFIL ADMIN
    Route::prefix('profil')->name('profil.')->group(function () {
        Route::get('/', [AdminProfilController::class, 'index'])->name('index');
        Route::post('update', [AdminProfilController::class, 'update'])->name('update');
        Route::post('destroy', [AdminProfilController::class, 'destroy'])->name('destroy');
    });
    Route::resource('kategori-pertanyaan', QuestionCategoryController::class);


});


// --- ROUTE PUBLIC --- //
Route::get('/layanan-pelanggan-pelapor', [CSController::class, 'showCreateForm'])
    ->name('layanan-pelanggan.index');
Route::get('/layanan-pelanggan-pelapor/create', [CSController::class, 'createRoom'])
    ->name('layanan-pelanggan.create');
Route::post('/layanan-pelanggan-pelapor/continue', [CSController::class, 'continueCS'])
    ->name('layanan-pelanggan.continue');
Route::post('/layanan-pelanggan-pelapor/assign-category/{code}', [CSController::class, 'assignCategory'])
    ->name('layanan-pelanggan.assign_category');
Route::post('/layanan-pelanggan-pelapor/set-admin/{id}', [CSController::class, 'setAdminStatus'])
    ->name('layanan-pelanggan.set_admin');
Route::post('/layanan-pelanggan-pelapor/finish/{code}', [CSController::class, 'userFinish'])
    ->name('layanan-pelanggan.finish');
Route::get('/layanan-pelanggan-pelapor/ruang/{code}', [CSController::class, 'userChat'])
    ->name('layanan-pelanggan.ruang_pesan');
Route::post('/login-all', [LoginAllController::class, 'loginAjax'])
    ->name('login.ajax');
Route::post('/layanan-pelanggan-pelapor/panggil-lagi/{room}', [CSController::class, 'panggilLagi']);
Route::post('/layanan-pelanggan-pelapor/upload/{room}', [CSController::class, 'upload'])
    ->name('layanan-pelanggan.upload');// Ambil daftar file
Route::get('/layanan-pelanggan-pelapor/files/{room}', [CSController::class, 'listFiles'])
    ->name('layanan-pelanggan.files');







Route::middleware(['auth'])->prefix('admin')->group(function () {

    Route::get('/layanan-pelanggan-admin', [CSAdminController::class, 'adminIndex'])
        ->name('admin.layanan-pelanggan.index');

    Route::get('/layanan-pelanggan-admin/ruang/{code}', [CSAdminController::class, 'adminChat'])
        ->name('admin.layanan-pelanggan.ruang_pesan');

    Route::delete('/layanan-pelanggan-admin/{code}/hapus', [CSAdminController::class, 'adminDelete'])
        ->name('admin.layanan-pelanggan.delete');

    Route::get('/layanan-pelanggan-admin/riwayat', [CSAdminController::class, 'logsIndex'])
        ->name('admin.layanan-pelanggan.riwayat');

    Route::delete('/layanan-pelanggan-admin/riwayat/{id}/hapus', [CSAdminController::class, 'deleteLog'])
        ->name('admin.layanan-pelanggan.riwayat.delete');
    Route::get('/layanan-pelanggan-admin/files/{roomId}', [CSAdminController::class, 'listFiles'])
        ->name('admin.layanan-pelanggan.files');
    Route::post('/layanan-pelanggan-admin/upload/{roomId}', [CSAdminController::class, 'adminUpload'])
        ->name('admin.layanan-pelanggan.upload');

});
