<?php

use App\Http\Controllers\LpjController;
use App\Http\Controllers\ProkerController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\RabController;
use App\Http\Controllers\UsermanajemenController;
use App\Models\Organisasi;
use App\Models\Proker;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\OrganisasiController;
use App\Http\Controllers\UploadController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('login');
});
Route::post('login', [LoginController::class, 'signin']);
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('role:1');

Route::get('/usermanajemen/create', function () {
    return view('usermanajemen-create');
});

Route::post('/usermanajemen', [UsermanajemenController::class, 'store']);
Route::get('/usermanajemen', [UsermanajemenController::class, 'create'])->middleware('role:1');

Route::get('/dashboard/organisasi', function () {
    return view('dashboard-organisasi');
})->middleware('role:2');

Route::get('/dashboard/pengecek', function () {
    return view('dashboard-pengecek');
})->middleware('role:3');

Route::get('/dashboard/bpm', function () {
    return view('dashboard-bpm');
})->middleware('role:4');

Route::get('/usermanajemen/{user}', function (User $user) {
    return view('usermanajemen-edit', ['user' => $user, 'id' => $user->id]);
})->middleware('role:1');

Route::put('/usermanajemen/{user}', [UsermanajemenController::class, 'update'])->middleware('role:1');

Route::get('/organisasi/create', function () {
    return view('organisasi-create');
})->middleware('role:1');

Route::post('/organisasi', [OrganisasiController::class, 'store'])->middleware('role:1');

Route::delete('/usermanajemen/{id}', [UsermanajemenController::class, 'delete'])->middleware('role:1');

Route::get('/organisasi', [OrganisasiController::class, 'index'])->middleware('role:1');
Route::put('/organisasi/{organisasi}', [OrganisasiController::class, 'update'])->middleware('role:1');
Route::get('/organisasi/{organisasi}', function (Organisasi $organisasi) {
    return view('organisasi-edit', ['organisasi' => $organisasi, 'id' => $organisasi->id]);
})->middleware('role:1');
Route::delete('/organisasi/{id}', [OrganisasiController::class, 'delete'])->middleware('role:1');

Route::get('/proker', [ProkerController::class, 'index'])->middleware('role:2');
Route::get('/proker/create', [ProkerController::class, 'tampil'])->middleware('role:2');
Route::post('/proker', [ProkerController::class, 'store'])->middleware('role:2');
Route::put('/proker/{proker}', [ProkerController::class, 'update'])->middleware('role:2');
Route::get('/proker/{proker}', [ProkerController::class, 'edit'])->middleware('role:2');
Route::delete('/proker/{id}', [ProkerController::class, 'delete'])->middleware('role:2');


Route::get('/uploadlpj', [LpjController::class, 'index'])->middleware('role:2');
Route::get('/lihatlpj', [LpjController::class, 'indexlpj'])->middleware('role:1');
Route::get('/uploadrab', [RabController::class, 'index'])->middleware('role:2');
Route::get('/unduhsrpd', [RabController::class, 'unduhsrpd'])->middleware('role:2');
Route::get('/uploadproposal', [ProposalController::class, 'index'])->middleware('role:2');
Route::get('/lihatproposal', [ProposalController::class, 'indexproposal'])->middleware('role:1');
Route::get('/pengecekanproposal', [ProposalController::class, 'pengecekanproposal'])->middleware('role:3');
Route::get('/pengecekanproposalbpm', [ProposalController::class, 'pengecekanproposalbpm'])->middleware('role:4');
Route::get('/pengecekanlpj', [LpjController::class, 'pengecekanlpj'])->middleware('role:3');
Route::get('/pengecekanlpjbpm', [LpjController::class, 'pengecekanlpjbpm'])->middleware('role:4');
Route::get('/pengecekanrab', [RabController::class, 'uploadsrpd'])->middleware('role:4');


Route::post('/upload-file', [UploadController::class, 'store'])->name('file.upload');
