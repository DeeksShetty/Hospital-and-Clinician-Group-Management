<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GroupController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login',[AuthController::class,'login']);
Route::middleware('auth:sanctum')->group(function(){
    Route::post('/logout',[AuthController::class,'logout']);
    Route::group(['prefix'=>'groups'],function(){
        Route::post('/',[GroupController::class,'createGroup'])->name('group.create');
        Route::get('/',[GroupController::class,'getGroupList'])->name('group.list');
        Route::get('/{id}',[GroupController::class,'getGroupDetail'])->name('group.detail');
        Route::put('/{id}',[GroupController::class,'updateGroup'])->name('group.update');
        Route::delete('/{id}',[GroupController::class,'deleteGroup'])->name('group.delete');
    });
});
