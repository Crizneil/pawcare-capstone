<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Pet;
use Illuminate\Support\Facades\Auth;

class PetOwnerController extends Controller
{
    public function dashboard()
    {
        $pets = Auth::user()->pets()->with('vaccinations')->latest()->get();
        return view('pet-owner.dashboard', compact('pets'));
    }

    public function storePet(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'species' => 'required|string|max:255',
            'breed' => 'nullable|string|max:255',
            'birthdate' => 'nullable|date',
        ]);

        // Generate Unique ID
        $year = date('Y');
        $count = Pet::count() + 1;
        $unique_id = 'PC-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        Pet::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'species' => $request->species,
            'breed' => $request->breed,
            'birthdate' => $request->birthdate,
            'unique_id' => $unique_id,
            'registry_date' => now(),
        ]);

        return back()->with('success', 'Pet registered successfully! ID: ' . $unique_id);
    }

    public function digitalCard(Pet $pet)
    {
        // Ensure the authenticated user owns the pet
        if ($pet->user_id !== Auth::id()) {
            abort(403);
        }

        // Generate QR Code containing a URL to check it
        // We'll use the public profile URL
        $qrCode = QrCode::size(200)->generate(route('pet.public-profile', $pet->unique_id));

        return view('pet-owner.digital-card', compact('pet', 'qrCode'));
    }

    public function publicProfile($unique_id)
    {
        $pet = Pet::where('unique_id', $unique_id)->with(['user', 'vaccinations'])->firstOrFail();
        return view('pets.public-profile', compact('pet'));
    }
}
