<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\QrCheckinController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
});

// Publieke event routes
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event:slug}', [EventController::class, 'show'])->name('events.show');

// Webhook route (geen CSRF bescherming nodig)
Route::post('/webhook/mollie', [WebhookController::class, 'mollie'])->name('webhook.mollie');

// QR Check-in routes
Route::middleware('auth')->group(function () {
    Route::get('/qr-checkin/{event}', [QrCheckinController::class, 'show'])->name('qr-checkin.show');
    Route::post('/qr-checkin/{event}/scan', [QrCheckinController::class, 'scan'])->name('qr-checkin.scan');
    Route::post('/qr-checkin/{event}/manual', [QrCheckinController::class, 'manualCheckin'])->name('qr-checkin.manual');
    Route::get('/tickets/verify/{qrCode}', [QrCheckinController::class, 'verify'])->name('tickets.verify');
});

// Ticketverkoop routes
Route::middleware('auth')->group(function () {
    Route::post('/events/{event:slug}/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::get('/checkout/{ticket}', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/checkout/{ticket}/process', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/{ticket}/success', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/{ticket}/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel');
    
    // Mijn tickets
    Route::get('/my-tickets', [TicketController::class, 'myTickets'])->name('tickets.my-tickets');
    Route::get('/my-tickets/{ticket}/download', [TicketController::class, 'download'])->name('tickets.download');
});

// Feedback Routes
Route::get('/events/{event}/feedback', [FeedbackController::class, 'show'])->name('feedback.show');
Route::get('/events/{event}/feedback/create', [FeedbackController::class, 'create'])->name('feedback.create');
Route::post('/events/{event}/feedback', [FeedbackController::class, 'store'])->name('feedback.store');
Route::get('/events/{event}/feedback/thank-you', [FeedbackController::class, 'thankYou'])->name('feedback.thank-you');

Route::middleware(['auth'])->group(function () {
    Route::get('/my-feedback', [FeedbackController::class, 'myFeedback'])->name('feedback.my-feedback');
});

// Admin Feedback Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/feedback', [FeedbackController::class, 'adminIndex'])->name('feedback.index');
    Route::get('/feedback/{feedback}', [FeedbackController::class, 'adminShow'])->name('feedback.show');
    Route::post('/feedback/{feedback}/approve', [FeedbackController::class, 'adminApprove'])->name('feedback.approve');
    Route::post('/feedback/{feedback}/reject', [FeedbackController::class, 'adminReject'])->name('feedback.reject');
});

// Dashboard Routes - Eén dashboard voor alle gebruikers met automatische redirects
Route::middleware(['auth', 'verified'])->group(function () {
    // Main dashboard route - RedirectBasedOnRole middleware zorgt voor juiste redirect
    Route::get('/dashboard', function () {
        $user = auth()->user();
        
        // Admin gaat naar admin panel
        if ($user->hasRole('admin')) {
            return redirect('/admin');
        }
        
        // Organizer gaat naar organizer dashboard
        if ($user->hasRole('organizer')) {
            return redirect('/organizer');
        }
        
        // Regular users zien de dashboard view
        return view('dashboard');
    })->name('dashboard');
    
    // Organizer dashboard (alleen voor organizers)
    Route::middleware(['role:organizer'])->group(function () {
        Route::get('/organizer-dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
        Route::get('/dashboard/export', [DashboardController::class, 'export'])->name('dashboard.export');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
