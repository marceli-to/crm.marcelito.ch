<?php
use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Route;

Volt::route('/timer', 'timer.index')->name('timer');
Volt::route('/projects', 'projects.index')->name('projects');
Volt::route('/companies', 'companies.index')->name('companies');
Volt::route('/expenses', 'expenses.index')->name('expenses');
// Route::get('/', function () {
//     return view('welcome');
// });
