<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

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
use App\Http\Controllers\Admin\ComplaintController;
use App\Http\Controllers\Admin\ImportMateriController;

//  ROUTE AWAL
Route::get('/', fn() => redirect('/login'));
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.process');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

//  ROUTE DENGAN AUTHENTICATION + PREFIX ADMIN
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    //  DASHBOARD
    Route::get('/dashboard', fn() => view('admin.dashboard'))->name('dashboard');

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
    Route::prefix('pra_latihan')->name('pra_latihan.')->group(function () {
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
    Route::prefix('pelajaran/{lesson_id}/latihan_soal')->name('pelajaran.latihan_soal.')->group(function () {

        //  Daftar Latihan
        Route::get('/', [ExerciseController::class, 'index'])->name('index');
        Route::get('/create', [ExerciseController::class, 'create'])->name('create');
        Route::post('/', [ExerciseController::class, 'store'])->name('store');
        Route::get('/{exercise_id}/edit', [ExerciseController::class, 'edit'])->name('edit');
        Route::put('/{exercise_id}', [ExerciseController::class, 'update'])->name('update');
        Route::delete('/{exercise_id}', [ExerciseController::class, 'destroy'])->name('destroy');

        //  Soal di dalam Latihan
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
    Route::resource('serial', SerialController::class);
    Route::post('/serial/{id}/extend', [SerialController::class, 'extend'])->name('serial.extend');

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

    //  COMPLAINT
    Route::resource('complaint', ComplaintController::class);
});
