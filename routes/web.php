<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PaperController;
use App\Http\Controllers\ConferenceController;
use App\Http\Controllers\ReviewerController;
use App\Http\Controllers\NotificationController;


/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/auth/redirect/{provider}', [AuthController::class, 'redirectToProvider'])->name('oauth.redirect');
Route::get('/auth/callback/{provider}', [AuthController::class, 'handleProviderCallback'])->name('auth.callback');

/*
|--------------------------------------------------------------------------
| Registration
|--------------------------------------------------------------------------
*/
Route::get('/register', [UserController::class, 'register'])->name('register');
Route::post('/register', [UserController::class, 'store'])->name('user.store');

/*
|--------------------------------------------------------------------------
| Protected Routes
|--------------------------------------------------------------------------
*/
        Route::middleware('auth')->group(function () {

        Route::middleware('auth')->group(function () {

            // ---------- Notifications ----------
            Route::prefix('notifications')->group(function () {
                // List all notifications
                Route::get('/', [NotificationController::class, 'index'])->name('notifications.index');
                Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy')->middleware('auth');

                // Mark a notification as read
                Route::get('/mark-as-read/{id}', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
            });

        });

    /*
    |--------------------------------------------------------------------------
    | 🧑‍💼 Admin Section
    |--------------------------------------------------------------------------
    */
    Route::middleware('can:isAdmin')->group(function () {

        // Dashboard
        Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');

        // User Management
        Route::prefix('users')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('admin.users.index');
            Route::post('/{user}/assign-role', [UserController::class, 'assignRole'])->name('admin.assignRole');
        });

        // Conference Management
        Route::prefix('conferences')->group(function () {
            Route::get('/', [ConferenceController::class, 'index'])->name('conference.index');
            Route::get('/create', [ConferenceController::class, 'create'])->name('conference.create');
            Route::post('/', [ConferenceController::class, 'store'])->name('conference.store');
            Route::get('/conference/{conference_code}/edit', [ConferenceController::class, 'edit'])->name('conference.edit');
            Route::put('/conference/{conference_code}/update', [ConferenceController::class, 'update'])->name('conference.update');
            Route::delete('/{conference_code}', [ConferenceController::class, 'destroy'])->name('conference.destroy');
            Route::delete('/mass-destroy', [ConferenceController::class, 'massDestroy'])->name('conference.massDestroy');

            // Manage reviewers
            Route::get('/{conference_code}/edit-reviewers', [ConferenceController::class, 'editReviewers'])->name('conference.editReviewers');
            Route::put('/{conference_code}/update-reviewers', [ConferenceController::class, 'updateReviewers'])->name('conference.updateReviewers');

            // View conference papers
            Route::get('/{conference_code}/papers', [ConferenceController::class, 'showPapers'])->name('conference.papers');
        });

        // Assign reviewer to a paper
        Route::post('/papers/{paper}/assign-reviewer', [PaperController::class, 'assignReviewer'])->name('admin.assignReviewer');
    });

    /*
    |--------------------------------------------------------------------------
    | 🧑‍⚖️ Reviewer Section
    |--------------------------------------------------------------------------
    */
    Route::prefix('reviewer')->name('reviewer.')->group(function () {
        Route::get('/home', [ReviewerController::class, 'index'])->name('home');

        // Conference papers
        Route::get('/conference/{conference_code}/papers', [ReviewerController::class, 'showConferencePapers'])->name('conference.papers');

        // Review form
        Route::get('/review/{paper_id}', [ReviewerController::class, 'reviewForm'])->name('reviewForm');

        // Submit review
        Route::post('/review/{paper_id}', [ReviewerController::class, 'submitReview'])->name('submitReview');
    });

    /*
    |--------------------------------------------------------------------------
    | 🧑‍🎓 Author Section
    |--------------------------------------------------------------------------
    */
    Route::prefix('author')->group(function () {
        Route::get('/home', function () {
            $userId = auth()->id();
            $conferences = \App\Models\Conference::all();
            $papers = \App\Models\Paper::with(['conference', 'reviews.reviewer'])
                        ->where('author_id', $userId)->get();
            return view('home', compact('conferences', 'papers'));
        })->name('home');

        Route::get('/my-papers', [PaperController::class, 'mySubmissions'])->name('author.myPapers');
    });

    /*
    |--------------------------------------------------------------------------
    | 📄 Paper Routes (Shared by Authors)
    |--------------------------------------------------------------------------
    */
    Route::prefix('papers')->group(function () {
        Route::get('/submit/{conference_code}', [PaperController::class, 'create'])->name('paper.submit');
        Route::post('/submit', [PaperController::class, 'store'])->name('paper.store');

        Route::get('/{id}/show', [PaperController::class, 'show'])->name('paper.show');
        Route::get('/{id}/view', [PaperController::class, 'viewFile'])->name('paper.viewFile');

        Route::get('/{paper_id}/edit', [PaperController::class, 'edit'])->name('paper.edit');
        Route::put('/{paper_id}', [PaperController::class, 'update'])->name('paper.update');

        Route::get('/{paper}/resubmit', [PaperController::class, 'resubmit'])->name('paper.resubmit');
        Route::put('/{paper}/resubmit', [PaperController::class, 'resubmitUpdate'])->name('paper.resubmitUpdate');
    });
});

/*
|--------------------------------------------------------------------------
| Fallback
|--------------------------------------------------------------------------
*/
Route::fallback(fn() => redirect()->route('login'));
