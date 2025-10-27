<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\ModuleController;
use Illuminate\Support\Facades\Route;



Route::get('/', function () {
    return view('welcome');
});


// Route::middleware(['auth'])->group(function () {
Route::resource('courses', CourseController::class);

Route::get('courses/{course}/modules', [ModuleController::class, 'index'])->name('courses.modules.index');
Route::post('courses/{course}/modules', [ModuleController::class, 'store'])->name('courses.modules.store');

//
// });
