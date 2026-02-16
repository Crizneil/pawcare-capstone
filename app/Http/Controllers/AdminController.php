<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Pet;
use App\Models\Vaccine;
use App\Models\PetVaccination;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Add stats for dashboard
        $totalPets = Pet::count();
        $totalOwners = User::where('role', 'owner')->count();
        $totalStaff = User::where('role', 'vet')->count();

        return view('admin.dashboard', compact('totalPets', 'totalOwners', 'totalStaff'));
    }

    public function enroll(Request $request)
    {
        $request->validate([
            'owner_email' => 'required|email',
            'pet_name' => 'required|string',
            'breed' => 'required|string',
            'next_date' => 'required|date',
        ]);

        // Find or Create Owner
        $owner = User::firstOrCreate(
            ['email' => $request->owner_email],
            [
                'name' => $request->owner_name ?? 'New Owner',
                'password' => Hash::make('password'), // Default password
                'role' => 'owner',
                'house_number' => 'N/A',
                'street' => 'N/A',
                'barangay' => 'N/A',
                'city' => 'Meycauayan',
            ]
        );

        // Generate Unique ID
        $year = date('Y');
        $count = Pet::count() + 1;
        $unique_id = 'PC-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        // Create Pet
        Pet::create([
            'user_id' => $owner->id,
            'name' => $request->pet_name,
            'species' => 'Dog',
            'breed' => $request->breed,
            'age' => 1,
            'unique_id' => $unique_id,
        ]);

        return back()->with('success', 'Pet enrolled successfully! ID: ' . $unique_id);
    }

    public function petRecords(Request $request)
    {
        $query = Pet::with(['user', 'vaccinations']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('unique_id', $search);
        }

        $pets = $query->latest()->paginate(10);
        return view('admin.pet-records', compact('pets'));
    }

    public function employees()
    {
        $staff = User::where('role', 'vet')->latest()->paginate(10);
        return view('admin.employees', compact('staff'));
    }

    public function storeEmployee(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'vet',
            // Default address for staff (can be updated later)
            'house_number' => 'Clinic',
            'street' => 'MacArthur Hwy',
            'barangay' => 'Clinic Brgy',
            'city' => 'City of Meycauayan',
            'province' => 'Bulacan',
        ]);

        return redirect()->route('admin.employees')->with('success', 'Staff member added successfully.');
    }

    public function vaccines(Request $request)
    {
        $query = Vaccine::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        }

        $vaccines = $query->paginate(10);

        return view('admin.vaccines', compact('vaccines'));
    }

    public function vaccinationStatus(Request $request)
    {
        $query = Pet::with(['user', 'vaccinations']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('unique_id', $search);
        }

        $pets = $query->latest()->paginate(10);

        return view('admin.vaccination-status', compact('pets'));
    }

    public function updateVaccine(Request $request, Vaccine $vaccine)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'stock' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
        ]);

        $vaccine->update($request->only(['name', 'stock', 'price']));

        return back()->with('success', 'Vaccine inventory updated successfully.');
    }

    public function recordVaccination(Request $request, Pet $pet)
    {
        $request->validate([
            'vaccine_name' => 'required|string|max:255',
            'administered_at' => 'required|date',
            'next_due_at' => 'nullable|date',
        ]);

        // Try to find matching vaccine in inventory to get price and update stock
        $vaccine = Vaccine::where('name', 'like', $request->vaccine_name)->first();
        $price = $vaccine ? $vaccine->price : null;

        if ($vaccine && $vaccine->stock > 0) {
            $vaccine->decrement('stock');
        }

        PetVaccination::create([
            'pet_id' => $pet->id,
            'vaccine_name' => $request->vaccine_name,
            'administered_at' => $request->administered_at,
            'next_due_at' => $request->next_due_at,
            'administered_by' => Auth::id(),
            'price' => $price,
        ]);

        return back()->with('success', 'Vaccination recorded successfully for ' . $pet->name);
    }

    public function updatePet(Request $request, Pet $pet)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'species' => 'required|string|max:255',
            'breed' => 'nullable|string|max:255',
            'birthdate' => 'nullable|date',
        ]);

        $pet->update($request->only(['name', 'species', 'breed', 'birthdate']));

        return back()->with('success', 'Pet information updated successfully.');
    }
}
