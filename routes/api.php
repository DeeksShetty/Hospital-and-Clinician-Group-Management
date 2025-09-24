<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GroupController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login',[AuthController::class,'login']);
Route::middleware('auth:sanctum')->group(function(){
    Route::post('/logout',[AuthController::class,'logout']);
    Route::group(['prefix'=>'groups'],function(){
        // Admin-only routes
        Route::post('/',[GroupController::class,'createGroup'])->name('group.create')->middleware('role:admin');
        Route::put('/{id}',[GroupController::class,'updateGroup'])->name('group.update')->middleware('role:admin');
        Route::delete('/{id}',[GroupController::class,'deleteGroup'])->name('group.delete')->middleware('role:admin');

        // Member and admin can view
        Route::get('/',[GroupController::class,'getGroupList'])->name('group.list');
        Route::get('/{id}',[GroupController::class,'getGroupDetail'])->name('group.detail');
    });
});
