<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StanzeController;
use App\Http\Controllers\IstruttoriController;
use App\Http\Controllers\LezioniController;
use App\Http\Controllers\AllieviController;
use App\Http\Controllers\PrenotazioniController;

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
    return view('welcome');
});

Route::get('/greeting', function () {
    return 'Hello World';
});

Route::get('/elenco-stanze', [StanzeController::class, 'index'])->name('elenco.stanze');

Route::get('/elenco-istruttori', [IstruttoriController::class, 'index'])->name('elenco.istruttori');

Route::get('/elenco-lezioni', [LezioniController::class, 'index'])->name('elenco.lezioni');
Route::post('/aggiungi-lezione', [LezioniController::class, 'store']);
Route::get('/lezioni/{id}/edit', [LezioniController::class, 'edit'])->name('edit.lezione');
Route::put('/lezioni/{id}', [LezioniController::class, 'update'])->name('update.lezione');
Route::delete('/lezioni/{id}', [LezioniController::class, 'destroy'])->name('destroy.lezione');

Route::get('/elenco-allievi', [AllieviController::class, 'index'])->name('elenco.allievi');
Route::post('/aggiungi-allievo', [AllieviController::class, 'store']);

Route::get('/elenco-prenotazioni', [PrenotazioniController::class, 'index'])->name('elenco.prenotazioni');
Route::post('/aggiungi-prenotazione', [PrenotazioniController::class, 'store']);


