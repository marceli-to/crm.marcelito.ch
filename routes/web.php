<?php
use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Route;

Volt::route('/projects', 'projects.index')->name('projects');
Volt::route('/companies', 'companies.index')->name('companies');

// Route::get('/', function () {
//     return view('welcome');
// });
