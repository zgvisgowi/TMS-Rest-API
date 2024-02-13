<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\TaskController;
use App\Http\Controllers\api\UserController;


Route::controller(UserController::Class)->group(function() {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('forgotPassword', 'forgotPassword');
});
Route::controller(TaskController::class)->name('task.')->middleware('auth:api')->group(function (){
    Route::get('/','index');
    Route::post("/create",'create')->name('create');
    Route::get('/{taskId}','read');
    Route::put("/{taskId}/update",'update');
    Route::delete('/{taskId}/delete','delete');
});
