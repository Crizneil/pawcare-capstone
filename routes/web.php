<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\VaccineController;
use App\Services\SmsService;
// SMS System is active in Services\SmsService.php

/*
|--------------------------------------------------------------------------
| Web Routes (PawCare)
|--------------------------------------------------------------------------
*/

// Public routes (views)
Route::view('/', 'index')->name('home');
Route::view('/about', 'about')->name('about');
Route::view('/blog', 'blog')->name('blog');
Route::view('/contact', 'contact')->name('contact');
Route::view('/faq', 'faq')->name('faq');
Route::view('/terms', 'terms')->name('terms');
Route::view('/privacy', 'privacy')->name('privacy');
Route::view('/service', 'service')->name('service');
Route::view('/service-single', 'service-single')->name('service.single');
Route::post('/contact-developer', [\App\Http\Controllers\ContactController::class, 'contactDeveloper'])->name('contact.developer');

// Publicly accessible pet profile for QR scanning (No Login Required)
Route::get('/verify-pet/{pet_id}', [PetController::class, 'publicProfile'])->name('pet.public-profile');

// Keep old static HTML links working (redirects)
Route::redirect('/index.html', '/');
Route::redirect('/about.html', '/about');
Route::redirect('/blog.html', '/blog');
Route::redirect('/contact.html', '/contact');
Route::redirect('/faq.html', '/faq');
Route::redirect('/terms.html', '/terms');
Route::redirect('/privace.html', '/privacy');
Route::redirect('/service.html', '/service');
Route::redirect('/service-single.html', '/service-single');


// Authentication Routes
Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AdminAuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
Route::get('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
Route::post('/register', [AdminAuthController::class, 'register'])->name('register.post');


// Protected Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    // Dashboard (Handled by AdminController to get the stats)
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Actions
    Route::post('/staff/store', [AdminController::class, 'storeStaff'])->name('staff.store');
    Route::post('/search-pet', [AdminController::class, 'searchPet'])->name('search-pet');
    Route::get('/employees', [AdminController::class, 'employees'])->name('employees');
    Route::put('/staff/update/{id}', [AdminController::class, 'updateStaff'])->name('staff.update');
    Route::delete('/staff/delete/{id}', [AdminController::class, 'destroyStaff'])->name('staff.destroy');
    Route::get('/appointments', [AdminController::class, 'appointments'])->name('appointments');
    Route::post('/appointments/store', [AdminController::class, 'storeAppointment'])->name('appointments.store');
    Route::get('/appointments/create', [AdminController::class, 'createAppointment'])->name('appointments.create');
    Route::post('/appointments/{id}/approve', [AdminController::class, 'approve'])->name('appointments.approve');
    Route::post('/appointments/{id}/reject', [AdminController::class, 'reject'])->name('appointments.reject');
    Route::post('/appointments/{id}/cancel', [AdminController::class, 'cancel'])->name('appointments.cancel');
    Route::post('/appointments/{id}/reschedule', [AdminController::class, 'reschedule'])->name('appointments.reschedule');
    Route::get('/owners', [AdminController::class, 'owners'])->name('owners');
    Route::put('/pets/update/{id}', [AdminController::class, 'updatePet'])->name('pets.update');
    Route::delete('/pets/delete/{id}', [AdminController::class, 'destroyPet'])->name('pets.destroy');
    Route::get('/pet-records', [AdminController::class, 'petRecords'])->name('pet-records');
    Route::post('/logs/archive', [AdminController::class, 'archiveLogs'])->name('logs.archive');
    Route::post('/logs/{id}/restore', [AdminController::class, 'restoreLog'])->name('logs.restore');
    Route::post('/logs/restore-all', [AdminController::class, 'restoreAllLogs'])->name('logs.restore-all');
    Route::get('/vaccination-status', [VaccineController::class, 'status'])->name('vaccination-status');
    Route::get('/vaccinations', [VaccineController::class, 'index'])->name('vaccinations');
    Route::put('/vaccination/update/{id}', [VaccineController::class, 'updateStatus'])->name('vaccinations.update');
    Route::patch('/vaccine/{id}/update', [AdminController::class, 'updateVaccine'])->name('vaccine.update');
    Route::delete('/vaccine/{id}/delete', [AdminController::class, 'destroyVaccine'])->name('vaccine.destroy');
    Route::post('/vaccine/store', [AdminController::class, 'storeVaccine'])->name('vaccine.store');
    Route::post('/pets/{id}/vaccinate', [VaccineController::class, 'store'])->name('pets.vaccinate');
    Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
    Route::put('/profile/update', [AdminController::class, 'updateProfile'])->name('profile.update');
    Route::post('/enroll', [AdminController::class, 'enroll'])->name('enroll');
});

// Staff Specific Routes
Route::prefix('staff')->name('staff.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [StaffController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [StaffController::class, 'profile'])->name('profile');

    // Appointments
    Route::get('/appointments', [StaffController::class, 'appointments'])->name('appointments');
    Route::post('/appointments/{id}/status/{status}', [StaffController::class, 'updateAppointmentStatus'])->name('appointments.update');
    Route::post('/appointments/store', [StaffController::class, 'storeAppointment'])->name('appointments.store');

    // Pet Records & Vaccination
    Route::get('/pet-records', [StaffController::class, 'petRecords'])->name('pet-records');
    Route::get('/vaccination-status', [StaffController::class, 'vaccinationStatus'])->name('vaccination-status');
    Route::get('/vaccination-history', [StaffController::class, 'vaccinationHistory'])->name('vaccination-history');
    Route::post('/vaccination/store/{id}', [StaffController::class, 'updateVaccination'])->name('vaccination.store');

    // Inventory
    Route::get('/vaccine-inventory', [StaffController::class, 'vaccineInventory'])->name('vaccine-inventory');
    Route::post('/vaccine-inventory/{id}/use', [StaffController::class, 'useVaccineInventory'])->name('vaccine-use');
    Route::patch('/vaccine-inventory/{id}/update', [StaffController::class, 'updateVaccine'])->name('vaccine.update');

    // Digital Card Requests
    Route::post('/request-card/{pet_id}', [StaffController::class, 'requestDigitalCard'])->name('request-card');

});

// Pet Owner Routes
Route::prefix('owner')->name('pet-owner.')->middleware(['auth'])->group(function () {
    // This points to the dashboard view we worked on
    Route::get('/dashboard', [PetController::class, 'ownerDashboard'])->name('dashboard');

    // Profile page (route name: pet-owner.profile)
    Route::get('/profile', [PetController::class, 'profile'])->name('profile');

    // Appointment booking page (route name: pet-owner.appointments)
    Route::get('/appointments', [PetController::class, 'appointments'])->name('appointments');
    Route::post('/appointments/book', [PetController::class, 'book'])->name('appointments.book');
    Route::patch('/appointments/{id}/cancel', [PetController::class, 'cancelAppointment'])->name('appointments.cancel');
    Route::get('/vaccination-history', [VaccineController::class, 'ownerHistory'])->name('vaccination-history');

    // Optional: Future pet records page for owners
    Route::get('/pet-records', [PetController::class, 'petRecords'])->name('pet-records');
    Route::get('/pet-records/{id}/digital-id', [PetController::class, 'showDigitalId'])->name('digital-id');
    Route::post('/pet-records/store', [PetController::class, 'store'])->name('pets.store');
});

