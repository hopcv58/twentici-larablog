<?php

use App\Http\Controllers\Backend\BlogController;
use App\Http\Controllers\Backend\CategoryController;
use App\Http\Controllers\Backend\HomeController;
use App\Http\Controllers\Backend\TagController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\FrontEndController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [FrontEndController::class, 'index'])->name('blog');

Route::get('/blog/{post:slug}', [FrontEndController::class, 'show'])->name('blog.show');

Route::post('/blog/{post:slug}', [FrontEndController::class, 'comment'])->name('blog.comment');

Route::get('/category/{category:slug}', [FrontEndController::class, 'category'])->name('category');

Route::get('/author/{author:slug}', [FrontEndController::class, 'author'])->name('author');

Route::get('/tag/{tag:slug}', [FrontEndController::class, 'tag'])->name('tag');

Auth::routes();

Route::get('/backend/home', [HomeController::class, 'index'])->name('home');

Route::name('backend.')->group(function () {
    Route::resource('/backend/blog', BlogController::class);
    Route::put('/backend/blog/restore/{blog}', [BlogController::class, 'restore'])->name('blog.restore');
    Route::delete('/backend/blog/force-destroy/{blog}', [BlogController::class, 'forceDestroy'])->name('blog.force-destroy');
    Route::resource('/backend/category', CategoryController::class);
    Route::resource('/backend/user', UserController::class);
    Route::get('/backend/user/confirm/{user}', [UserController::class, 'confirm'])->name('user.confirm');
    Route::get('backend/account/edit', [HomeController::class, 'edit'])->name('account.edit');
    Route::put('backend/account/edit', [HomeController::class, 'update'])->name('account.update');
    Route::resource('/backend/tag', TagController::class);
});

