<?php

use App\Http\Controllers\Post\PostController;
use App\Http\Controllers\Post\LikeController;
use App\Http\Controllers\Post\RePostController;
use App\Http\Controllers\Post\ReplyController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\FollowController;
use App\Http\Controllers\User\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//Posting
Route::resource('posts', PostController::class)
    ->only(['index', 'store', 'update'])
    ->middleware(['auth', 'verified']);
Route::get('posts/{Post}/show', [PostController::class, 'show'])->name('posts.show');
Route::delete('posts/{Post}/destroy', [PostController::class, 'destroy'])->name('posts.destroy');

Route::middleware(['auth', 'verified'])->group(function (){
    Route::post('Posts/{Post}/reply', [ReplyController::class, 'store'])->name('Posts.reply');
});

//Post actions
Route::middleware(['auth', 'verified'])->group(function () {
    Route::patch('posts/{Post}/like', [LikeController::class, 'like'])->name('posts.like');
    Route::patch('posts/{Post}/unlike', [LikeController::class, 'dislike'])->name('posts.unlike');
    Route::patch('posts/{Post}/toggle-like', [LikeController::class, 'toggle'])->name('posts.toggle-like');
    Route::post('posts/{Post}/rePost', [RePostController::class, 'rePost'])->name('posts.rePost');
    Route::post('posts/{Post}/undo-rePost', [RePostController::class, 'undo_rePost'])->name('posts.undo_rePost');

});

//notifications
Route::middleware(['auth', 'verified'])->group(function (){
    Route::get('notifications', [NotificationsController::class, 'index'])
        ->name('notifications.index');
    Route::patch('notifications/mark-all-as-read', [NotificationsController::class, 'markAllAsRead'])
        ->name('notifications.mark-all-as-read');

    Route::patch('notifications/{notification}/mark-as-read', [NotificationsController::class, 'markAsRead'])
        ->name('notifications.mark-as-read');
    Route::delete('notifications/{notification}', [NotificationsController::class, 'destroy'])
        ->name('notifications.destroy');

});

//Users
Route::middleware(['auth', 'verified'])->group(function (){
    Route::get('users', [UserController::class, 'index'])
        ->name('users.index');
    Route::get('users/{user}', [UserController::class, 'show'])
        ->name('users.show');

    Route::get('users/{user}/following', [FollowController::class, 'following'])
        ->name('users.following');
     Route::get('users/{user}/followers', [FollowController::class, 'followers'])
        ->name('users.followers');


    Route::post('users/{user}/follow', [FollowController::class, 'create'])
        ->name('users.follow');

    Route::post('users/{user}/unfollow', [FollowController::class, 'destroy'])
        ->name('users.unfollow');

    Route::post('users/{user}/toggle-follow', [FollowController::class, 'toggleFollow'])
        ->name('users.toggle-follow');
});
require __DIR__.'/auth.php';
