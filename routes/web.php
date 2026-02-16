<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PetOwnerController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\VetController;
use App\Http\Controllers\Auth\PetOwnerAuthController;

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
Route::view('/scan', 'public-scan')->name('public-scan');
Route::get('/p/{unique_id}', [PetOwnerController::class, 'publicProfile'])->name('pet.public-profile');

// Unified Login Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Redirects are removed to prevent automatic redirection to login/home from old links

// Middlewares and Dashboards

Route::middleware(['auth'])->group(function () {
    // Admin Dashboard
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::post('/admin/enroll', [AdminController::class, 'enroll'])->name('admin.enroll');

    // Vet Routes
    Route::get('/vet/dashboard', [VetController::class, 'dashboard'])->name('vet.dashboard');
    Route::get('/vet/pet-records', [VetController::class, 'petRecords'])->name('vet.pet-records');
    Route::get('/vet/vaccination-status', [VetController::class, 'vaccinationStatus'])->name('vet.vaccination-status');
    Route::get('/vet/vaccinations', [VetController::class, 'vaccines'])->name('vet.vaccinations');
    Route::post('/vet/search-pet', [VetController::class, 'searchPet'])->name('vet.search-pet');

    // Admin Features
    Route::get('/admin/employees', [AdminController::class, 'employees'])->name('admin.employees');
    Route::post('/admin/employees', [AdminController::class, 'storeEmployee'])->name('admin.employees.store');

    Route::get('/admin/pet-records', [AdminController::class, 'petRecords'])->name('admin.pet-records');

    Route::get('/admin/vaccination-status', [AdminController::class, 'vaccinationStatus'])->name('admin.vaccination-status');
    Route::get('/admin/vaccinations', [AdminController::class, 'vaccines'])->name('admin.vaccinations');

    // Placeholders
    Route::view('/admin/pet-owners', 'admin.pet-owners')->name('admin.pet-owners');
    Route::view('/admin/appointments', 'admin.appointments')->name('admin.appointments');
    Route::view('/admin/appointments/create', 'admin.appointments-create')->name('admin.appointments.create');
    Route::view('/admin/vaccinations/create', 'admin.vaccinations-create')->name('admin.vaccinations.create');
    Route::view('/admin/reports', 'admin.reports')->name('admin.reports');
    Route::view('/admin/settings', 'admin.settings')->name('admin.settings');
    Route::post('/admin/pets/{pet}/vaccinate', [AdminController::class, 'recordVaccination'])->name('admin.pets.vaccinate');
    Route::put('/admin/pets/{pet}', [AdminController::class, 'updatePet'])->name('admin.pets.update');
    Route::put('/admin/vaccines/{vaccine}', [AdminController::class, 'updateVaccine'])->name('admin.vaccines.update');
});

// Pet Owner Routes
Route::get('/pet-owner/register', [PetOwnerAuthController::class, 'showRegisterForm'])->name('pet-owner.register');
Route::post('/pet-owner/register', [PetOwnerAuthController::class, 'register'])->name('pet-owner.register.post');
// Allow existing direct login for owners if they bookmark it, but main login is unified
Route::get('/pet-owner/login', [PetOwnerAuthController::class, 'showLoginForm'])->name('pet-owner.login');
Route::post('/pet-owner/login', [PetOwnerAuthController::class, 'login'])->name('pet-owner.login.post');

Route::middleware('auth')->group(function () {
    Route::get('/pet-owner/dashboard', [PetOwnerController::class, 'dashboard'])->name('pet-owner.dashboard');
    Route::post('/pet-owner/register-pet', [PetOwnerController::class, 'storePet'])->name('pet-owner.register-pet');
    Route::get('/pet-owner/digital-card/{pet}', [PetOwnerController::class, 'digitalCard'])->name('pet-owner.digital-card');
    Route::post('/pet-owner/logout', [PetOwnerAuthController::class, 'logout'])->name('pet-owner.logout');
});


