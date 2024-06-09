<?php

use App\Http\Controllers\AnggaranController;
use App\Http\Controllers\FormatController;
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
use App\Http\Controllers\Controller;
use App\Http\Controllers\Auth\ResetPasswordController;


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

Route::post('/create-signature-pdf', [ProposalController::class, 'createSignaturePdf'])->name('createSignaturePdf');
Route::get('/proposals/approve/{proposalId}', [ProposalController::class, 'approvedProposal'])->name('proposals.approve');
Route::post('/proposals/revisi', [ProposalController::class, 'updateRevisi'])->name('proposals.revisi');
Route::get('/download-signature-pdf', [Controller::class, 'downloadSignaturePdf']);

Route::get('/', function () {
    return view('login');
});
Route::post('login', [LoginController::class, 'signin']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('role:1');

Route::get('/usermanajemen/create', [UsermanajemenController::class, 'createform'])->middleware('role:1');
Route::post('/usermanajemen', [UsermanajemenController::class, 'store'])->middleware('role:1');
Route::get('/usermanajemen', [UsermanajemenController::class, 'create'])->middleware('role:1');
Route::get('/usermanajemen/{user}', [UsermanajemenController::class, 'edit'])->middleware('role:1');
Route::put('/usermanajemen/{user}', [UsermanajemenController::class, 'update'])->middleware('role:1');

Route::get('/dashboard/organisasi', function () {
    return view('dashboard-organisasi');
})->middleware('role:2');

Route::get('/dashboard/pengecek', function () {
    return view('dashboard-pengecek');
})->middleware('role:3');

Route::get('/dashboard/bpm', function () {
    return view('dashboard-bpm');
})->middleware('role:4');

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
Route::get('/pengecekanrab', [RabController::class, 'uploadsrpd'])->middleware('role:4');
Route::get('/unduhsrpd', [RabController::class, 'unduhsrpd'])->middleware('role:2');
Route::get('/uploadproposal', [ProposalController::class, 'index'])->middleware('role:2');
Route::get('/lihatproposal', [ProposalController::class, 'indexproposal'])->middleware('role:1');
Route::get('/pengecekanproposal', [ProposalController::class, 'pengecekanproposal'])->middleware('role:3');
Route::get('/pengecekanproposalbpm', [ProposalController::class, 'pengecekanproposalbpm'])->middleware('role:4');
Route::get('/pengecekanlpj', [LpjController::class, 'pengecekanlpj'])->middleware('role:3');
Route::get('/pengecekanlpjbpm', [LpjController::class, 'pengecekanlpjbpm'])->middleware('role:4');

Route::post('/upload-file', [UploadController::class, 'store'])->name('file.upload');
Route::post('/uploadrab-file', [RabController::class, 'uploadrab'])->name('filerab.upload');
Route::post('/upload-lpj', [LpjController::class, 'store'])->name('filelpj.upload');
Route::post('/pengecekan-rab/{id}', [RabController::class, 'upsrpd'])->name('filesrpd.upload');

Route::get('/pengecekan-lpj', [LpjController::class, 'pengecekanlpj'])->name('pengecekan-lpj.index');
Route::match(['get', 'post'], '/lpj/approve/{lpjId}', [LpjController::class, 'approvedLpj'])->name('lpjs.approve');
Route::post('/lpj/revisi', [LpjController::class, 'updateRevisiLpj'])->name('lpjs.revisi');
Route::post('/lpj/signature', [LpjController::class, 'createSignaturePdf'])->name('createSignaturePdfLpj');

Route::get('password/reset', [ResetPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ResetPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
Route::get('forgot-password', [ResetPasswordController::class, 'showreset']);

Route::get('/file-format', [FormatController::class, 'index'])->middleware('role:4');
Route::post('/file-format', [FormatController::class, 'store'])->name('format.store')->middleware('role:4');
Route::post('/format/{id}', [FormatController::class, 'update'])->name('format.update')->middleware('role:4');
Route::delete('/file-format/{id}', [FormatController::class, 'delete'])->name('file-format.delete')->middleware('role:4');
Route::get('/file/format', [FormatController::class, 'indexformat'])->middleware('role:2');

Route::get('/anggaran', [AnggaranController::class, 'index'])->middleware('role:4');
Route::post('/anggaran/store', [AnggaranController::class, 'store'])->name('anggaran.store')->middleware('role:4');
Route::post('/anggaran/update/{id}', [AnggaranController::class, 'update'])->name('anggaran.update')->middleware('role:4');
Route::delete('/anggaran/delete/{id}', [AnggaranController::class, 'delete'])->name('anggaran.delete')->middleware('role:4');

Route::get('/anggaran/organisasi', [AnggaranController::class, 'indexanggaranorganisasi'])->middleware('role:2,3');
