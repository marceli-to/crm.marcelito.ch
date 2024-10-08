<?php
use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PdfController;
Volt::route('/timer', 'timer.index')->name('timer');
Volt::route('/projects', 'projects.index')->name('projects');
Volt::route('/companies', 'companies.index')->name('companies');
Volt::route('/expenses', 'expenses.index')->name('expenses');

Route::get('/pdf/expense/{expense}', [PdfController::class, 'expense'])->name('pdf.expense');

// Route::get('/', function () {
//     return view('welcome');
// });
