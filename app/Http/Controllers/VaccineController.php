<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pet;
use App\Models\Vaccination;
use App\Models\Appointment;
use App\Models\VaccineInventory;

class VaccineController extends Controller
{
    public function index(Request $request)
    {
    $query = VaccineInventory::query();

    if ($request->search) {
        $query->where('name', 'like', '%' . $request->search . '%')
              ->orWhere('batch_no', 'like', '%' . $request->search . '%');
    }

    $vaccines = $query->latest()->paginate(10);

    //  Detect if we are on the staff page or admin page
    if ($request->is('staff/*')) {
        return view('staff.vaccine-inventory', compact('vaccines'));
    }

    return view('admin.vaccinations', compact('vaccines'));
    }

    public function status(Request $request)
    {
    $query = Pet::with(['user', 'latestVaccination']);

    if ($request->search) {
        $query->where('name', 'like', '%' . $request->search . '%')
              ->orWhere('pet_id', 'like', '%' . $request->search . '%');
    }

    $pets = $query->latest()->paginate(10);

    return view('admin.vaccination-status', compact('pets'));
    }
    public function store(Request $request, $id)
    {
    $request->validate([
        'vaccine_name' => 'required|string|max:255',
        'date_administered' => 'required|date',
        'next_due_date' => 'nullable|date|after:date_administered',
        'status' => 'required|string',
    ]);

    Vaccination::create([
        'pet_id' => $id,
        'vaccine_name' => $request->vaccine_name,
        'date_administered' => $request->date_administered,
        'next_due_date' => $request->next_due_date,
        'status' => $request->status, // Save the status!
        'batch_no' => $request->batch_no ?? null,
        'remarks' => $request->remarks ?? null,
        'staff_id' => auth()->id(), // Track who did it
    ]);

    return back()->with('success', 'Vaccination record added successfully!');
    }

    public function update(Request $request, $id)
    {
    $request->validate([
        'stock' => 'required|integer|min:0',
        'expiry_date' => 'required|date',
    ]);

    $vaccine = VaccineInventory::findOrFail($id);

    // Only update stock and expiry for staff level
    $vaccine->update([
        'stock' => $request->stock,
        'expiry_date' => $request->expiry_date,
    ]);

    return redirect()->route('staff.vaccine-inventory')
                     ->with('success', 'Vaccine stock updated successfully.');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'vaccine_name' => 'required|string|max:255',
            'date_administered' => 'required|date',
            'next_due_date' => 'nullable|date',
            'batch_no' => 'nullable|string',
            'remarks' => 'nullable|string'
        ]);

        $pet = Pet::findOrFail($id);

        // --- AUTOMATION LOGIC ---
        $status = 'fully_vaccinated'; // Default
        if ($request->next_due_date) {
            $dueDate = \Carbon\Carbon::parse($request->next_due_date);
            $now = \Carbon\Carbon::now();

            if ($now->gt($dueDate)) {
                $status = 'overdue';
            } elseif ($now->diffInDays($dueDate) <= 14) {
                $status = 'due_soon';
            }
        }
        // --- INVENTORY REDUCTION LOGIC ---
        $inventory = VaccineInventory::where('name', $request->vaccine_name)->first();
        if ($inventory) {
            if ($inventory->stock > 0) {
                $inventory->decrement('stock', 1);
            } else {
                return back()->with('error', "Insufficient stock for {$request->vaccine_name}!");
            }
        }

        $pet->vaccinations()->create([
            'vaccine_name'      => $request->vaccine_name,
            'date_administered' => $request->date_administered,
            'next_due_date'     => $request->next_due_date,
            'status'            => $status, // Use the calculated status
            'batch_no'          => $request->batch_no,
            'remarks'           => $request->remarks,
            'admin_id'          => auth()->id(),
        ]);

        return back()->with('success', "Vaccination record for {$pet->name} updated!");
    }
    public function ownerHistory()
    {
    // Get the IDs of all pets belonging to the logged-in user
    $petIds = Pet::where('user_id', auth()->id())->pluck('id');

    // Fetch vaccination records for those pets
    $vaccinations = Vaccination::whereIn('pet_id', $petIds)
        ->with('pet') // Assuming you have a 'pet' relationship in your Vaccination model
        ->latest('date_administered')
        ->paginate(10);

    return view('pet-owner.vaccination-history', compact('vaccinations'));
    }

    public function cancel($id)
    {
    $appointment = Appointment::where('id', $id)
        ->where('user_id', auth()->id())
        ->firstOrFail();

    if ($appointment->status === 'pending') {
        $appointment->update(['status' => 'cancelled']);
        return back()->with('success', 'Appointment cancelled successfully.');
    }

    return back()->with('error', 'Only pending appointments can be cancelled.');
    }
}

