<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\Appointment;
use App\Models\User;
use App\Models\UserRequest;
use App\Models\Vaccination;
use App\Models\VaccineInventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;


class StaffController extends Controller
{
    public function dashboard()
    {
        return view('staff.dashboard', [
        // Fix: Only show approved appointments for today
        'appointmentsToday' => Appointment::whereDate('appointment_date', today())
            ->where('status', 'approved')
            ->get(),
            'dueForVaccination' => Pet::where('status', 'needs_booster')->limit(5)->get(),
            'lowStock' => VaccineInventory::whereColumn('stock', '<=', 'low_stock_threshold')->get(),
            'recentVaccinations' => Vaccination::with('pet')->latest()->limit(5)->get()
        ]);
    }

    public function appointments(Request $request)
    {
        $view = $request->get('view', 'today');
        $query = Appointment::with('user');

       $appointments = match($view) {
            // Fix: Only show approved for upcoming and today
            'upcoming' => $query->where('appointment_date', '>', today())
                                ->where('status', 'approved'),

            'completed' => $query->whereIn('status', ['Done', 'completed']),

            default => $query->whereDate('appointment_date', today())
                     ->whereIn('status', ['pending', 'approved']),
        };

        return view('staff.appointments', ['appointments' => $appointments->paginate(10), 'view' => $view]);
    }

    public function updateAppointmentStatus($id, $status)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->update(['status' => $status]);
        return back()->with('success', "Appointment marked as {$status}");
    }
    public function storeAppointment(Request $request)
    {
        $request->validate([
            'pet_name' => 'required|string|max:255',
            'species' => 'required',
            'service_type' => 'required',
            'email' => 'required_if:create_account,1|nullable|email|unique:users,email',
        ]);

        $userId = null;
        $ownerName = 'Guest'; // Default value for all walk-ins

        // 1. Handle Account Creation
        if ($request->has('create_account') && $request->email) {
            $user = User::create([
                'name' => $request->owner_name, // Use the name typed in the modal
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make('PawCare2026'),
                'role' => 'user',
            ]);
            $userId = $user->id;
            $ownerName = $user->name; // Set ownerName to the actual name for registered users
        }

        // 2. Create the Pet Record
        $pet = Pet::create([
            'user_id'    => $userId, // Will be NULL if no account created
            'pet_id'     => 'WALK-' . strtoupper(substr(uniqid(), -5)),
            'name'       => $request->pet_name,
            'species'    => $request->species,
            'gender'     => 'Unknown',
            'birthday'   => now(),
            'breed'      => 'Mixed/Other',
            'owner'      => $ownerName, // Saves "Guest" or the User's Name
            'status'     => 'ACTIVE',
        ]);

        // 3. Create the Appointment
        Appointment::create([
            'user_id'          => $userId,
            'pet_name'         => $pet->name,
            'species'          => $request->species,
            'appointment_date' => now()->toDateString(),
            'appointment_time' => now()->toTimeString(),
            'service_type'     => $request->service_type,
            'status'           => 'pending', // Default status for new walk-ins
        ]);

        return back()->with('success', 'Walk-in registered successfully!');
    }

    public function petRecords(Request $request)
    {
        $pets = Pet::with('user')
            ->when($request->search, function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('owner', 'like', "%{$request->search}%"); // This lets you search for "Guest"
            })
            ->latest()
            ->paginate(10);

        return view('staff.pet-records', compact('pets'));
    }

    public function vaccinationStatus(Request $request)
    {
        // Use with() to get relationships without excluding pets with null users
        $query = Pet::with(['user', 'latestVaccination']);

        // Filter by search (Pet name or Owner name)
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('owner', 'like', "%{$request->search}%");
            });
        }

        // Filter for pets vaccinated today
        if ($request->has('today')) {
            $query->whereHas('vaccinations', fn($q) => $q->whereDate('date_administered', today()));
        }

        $pets = $query->latest()->paginate(10);
        return view('staff.vaccination-status', compact('pets'));
    }

    public function vaccinationHistory(Request $request)
    {
        $query = Vaccination::with(['pet', 'staff']);

        // --- NEW: Filter by specific Pet ID ---
        if ($request->has('pet_id')) {
            $query->where('pet_id', $request->pet_id);
        }

        // Existing Filters
        if ($request->filter == 'today') {
            $query->whereDate('date_administered', today());
        }

        if ($request->filter == 'week') {
            $query->whereBetween('date_administered', [now()->startOfWeek(), now()->endOfWeek()]);
        }

        if ($request->staff_id) {
            $query->where('staff_id', $request->staff_id);
        }

        if ($request->vaccine_name) {
            $query->where('vaccine_name', $request->vaccine_name);
        }

        // Get results
        $history = $query->latest('date_administered')->paginate(15);

        // Get lists for the dropdown filters
        $staffList = User::where('role', 'staff')->get();
        $vaccineList = VaccineInventory::select('name')->distinct()->get();

        return view('staff.vaccination-history', compact('history', 'staffList', 'vaccineList'));
    }
    public function vaccineInventory()
    {
    $vaccines = VaccineInventory::latest()->get();
    return view('staff.vaccine-inventory', compact('vaccines'));
    }
    public function useVaccineInventory(Request $request, $id)
    {
        $vaccine = VaccineInventory::findOrFail($id);
        if ($vaccine->stock > 0) {
            $vaccine->decrement('stock', 1);
            return back()->with('success', "One dose of {$vaccine->name} deducted.");
        }
        return back()->with('error', 'Out of stock!');
    }
    public function updateVaccine(Request $request, $id)
    {
        $request->validate([
            'stock' => 'required|integer|min:0',
            'expiry_date' => 'required|date',
        ]);

        $vaccine = VaccineInventory::findOrFail($id);
        $vaccine->update([
            'stock' => $request->stock,
            'expiry_date' => $request->expiry_date,
        ]);

        return back()->with('success', "Inventory for {$vaccine->name} updated!");
    }

    public function profile()
    {
        $staff = Auth::user();
        return view('staff.profile', compact('staff'));
    }

    public function updateVaccination(Request $request, $id)
    {
        $request->validate([
            'vaccine_name' => 'required',
            'date_administered' => 'required|date',
            'next_due_date' => 'required|date',
        ]);
        // --- INVENTORY REDUCTION LOGIC ---
        // 1. Find the vaccine in the inventory
        $inventory = VaccineInventory::where('name', $request->vaccine_name)->first();

        // 2. Check if the vaccine exists and has stock
        if (!$inventory) {
            return back()->with('error', "Vaccine '{$request->vaccine_name}' not found in inventory.");
        }

        if ($inventory->stock <= 0) {
            return back()->with('error', "Cannot log vaccination: {$inventory->name} is out of stock!");
        }

        // 3. Deduct 1 from stock
        $inventory->decrement('stock', 1);

        // 4. Create the detailed history record
        Vaccination::create([
            'pet_id' => $id,
            'staff_id' => auth()->id(),
            'vaccine_name' => $request->vaccine_name,
            'date_administered' => $request->date_administered,
            'next_due_date' => $request->next_due_date,
        ]);

        // 5. Update the Pet's main record for quick viewing
        $pet = Pet::findOrFail($id);
        $pet->update([
            'vaccine_type' => $request->vaccine_name,
            'last_date' => $request->date_administered,
            'next_date' => $request->next_due_date,
        ]);

        return back()->with('success', "Vaccination logged! 1 dose of {$request->vaccine_name} deducted from inventory.");
    }

    public function requestDigitalCard(Request $request, $id)
    {
        // Validates and adds a new record to the Vaccination history
        $request->validate([
            'vaccine_name' => 'required|string',
            'date_administered' => 'required|date',
        ]);

        Vaccination::create([
            'pet_id' => $id,
            'vaccine_name' => $request->vaccine_name,
            'date_administered' => $request->date_administered,
            'next_due_date' => $request->next_due_date,
            'remarks' => $request->remarks,
        ]);

        return back()->with('success', 'Vaccination record added!');
    }
}
