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
            'dueForVaccination' => Pet::notDeceased()->where('status', 'needs_booster')->limit(5)->get(),
            'lowStock' => VaccineInventory::whereColumn('stock', '<=', 'low_stock_threshold')->get(),
            'recentVaccinations' => Vaccination::whereHas('pet', function ($q) {
                $q->notDeceased();
            })->with('pet')->latest()->limit(5)->get()
        ]);
    }

    public function appointments(Request $request)
    {
        $view = $request->get('view', 'today');
        $query = Appointment::with('user');

        $appointments = match ($view) {
            // Fix: Only show approved for upcoming and today
            'upcoming' => $query->where('appointment_date', '>', today())
                ->where('status', 'approved'),

            'completed' => $query->whereIn('status', ['Done', 'completed']),

            default => $query->whereDate('appointment_date', today())
                ->whereIn('status', ['pending', 'approved']),
        };
        $paginatedAppointments = $appointments->latest()
            ->paginate(10)
            ->appends(['view' => $view]);

        return view('staff.appointments', ['appointments' => $paginatedAppointments, 'view' => $view]);
    }

    public function updateAppointmentStatus($id, $status)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->status = $status;

        if (strtolower($status) === 'done' || strtolower($status) === 'completed') {
            // Find inventory matching the service type (e.g., 'Deworming' or 'Anti-Rabies')
            $inventory = VaccineInventory::where('name', $appointment->service_type)->first();

            $actualBatchNo = $inventory ? $inventory->batch_no : 'V-2026-' . strtoupper(substr(uniqid(), -4));

            $appointment->administered_by = auth()->user()->name;
            $appointment->batch_no = $actualBatchNo;
            $appointment->vaccine_name = $inventory ? $inventory->name : $appointment->service_type;
            $appointment->next_due_date = now()->addYear();

            if ($inventory && $inventory->stock > 0) {
                $inventory->decrement('stock', 1);
            }

            $pet = Pet::find($appointment->pet_id);
            if ($pet) {
                $pet->update([
                    'vaccine_type' => $appointment->service_type,
                    'last_date' => now(),
                    'next_date' => $appointment->next_due_date,
                ]);

                // Create the record in the History table
                Vaccination::create([
                    'pet_id' => $pet->id,
                    'staff_id' => auth()->id(),
                    'vaccine_name' => $appointment->service_type,
                    'date_administered' => now(),
                    'next_due_date' => $appointment->next_due_date,
                    'batch_no' => $actualBatchNo,
                    'status' => 'Up to Date'
                ]);
            }
        }

        $appointment->save();
        return back()->with('success', "Appointment updated and Vaccination History recorded!");
    }
    public function storeAppointment(Request $request)
    {
        $request->validate([
            'pet_name' => 'required|string|max:255',
            'species' => 'required',
            'gender' => 'required',
            'breed' => 'required',
            'service_type' => 'required',
            'email' => 'required_if:create_account,1|nullable|email|unique:users,email',
        ]);

        $userId = null;
        $ownerName = 'Guest'; // Default value for all walk-ins

        // 1. Handle Account Creation
        $ownerName = 'Guest';

        if ($request->has('create_account') && $request->email) {
            $user = User::create([
                'name' => $request->owner_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make('PawCare2026'),
                'role' => 'user',
            ]);
            $userId = $user->id;
            $ownerName = $user->name;
        }

        $finalBreed = ($request->breed === 'Other') ? $request->other_breed : $request->breed;

        $pet = Pet::create([
            'user_id' => $userId,
            'pet_id' => 'WALK-' . strtoupper(substr(uniqid(), -5)),
            'name' => $request->pet_name,
            'species' => $request->species,
            'gender' => $request->gender,
            'birthday' => $request->birthday ?? now(),
            'breed' => $finalBreed,
            'owner' => $ownerName,
            'status' => 'ACTIVE',
        ]);

        Appointment::create([
            'user_id' => $userId,
            'pet_id' => $pet->id,
            'pet_name' => $pet->name,
            'species' => $pet->species,
            'appointment_date' => now()->toDateString(),
            'appointment_time' => now()->toTimeString(),
            'service_type' => $request->service_type,
            'status' => 'approved',
        ]);

        return back()->with('success', 'Walk-in registered successfully!');
    }
    public function petRecords(Request $request)
    {
        $search = $request->query('search');
        $view = $request->input('view', 'active');

        // 1. CLEAN THE SEARCH TERM BEFORE THE QUERY
        if ($search && str_contains($search, '/verify-pet/')) {
            $search = last(explode('/verify-pet/', $search));
        }

        $query = Pet::with(['user', 'vaccinations']);

        // Check if $search is exactly a pet ID or internal ID
        // (Assuming if it matches 'PC-' or is numeric, it's likely a direct ID search)
        $isSpecificId = $search && (str_starts_with(strtoupper($search), 'PC-') || is_numeric($search));

        if ($isSpecificId) {
            $query->withTrashed()->where(function ($q) use ($search) {
                $q->where('id', $search)
                    ->orWhere('pet_id', 'like', "%{$search}%");
            });
        } else {
            if ($view === 'archived') {
                $query->withTrashed()->where(function ($q) {
                    $q->whereIn('status', ['DECEASED', 'INACTIVE'])
                        ->orWhereNotNull('deleted_at');
                });
            } else {
                $query->notDeceased();
            }

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('owner', 'like', "%{$search}%");
                });
            }
        }

        $pets = $query->latest()
            ->paginate(10)
            ->appends(['search' => $search, 'view' => $view]);

        return view('staff.pet-records', compact('pets', 'search', 'view'));
    }
    public function vaccinationStatus(Request $request)
    {
        // 1. Start the query with relationships
        $query = Pet::notDeceased()->with(['user', 'latestVaccination', 'appointments']);

        // Show pets that have EITHER an Approved appointment (ready for shot)
        // OR a Completed/Done appointment (so staff can see the history of what they just did)
        $query->whereHas('appointments', function ($q) {
            $q->whereIn('status', ['approved', 'Done', 'completed', 'rescheduled']);
        });

        // Search Filter
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('owner', 'like', "%{$request->search}%")
                    ->orWhere('pet_id', 'like', "%{$request->search}%");
            });
        }

        // Filter for pets vaccinated today
        if ($request->has('today')) {
            $query->whereHas('vaccinations', fn($q) => $q->whereDate('date_administered', today()));
        }

        $pets = $query->latest()->paginate(10)->appends($request->query());

        return view('staff.vaccination-status', compact('pets'));
    }

    public function vaccinationHistory(Request $request)
    {
        $query = Vaccination::whereHas('pet', function ($q) {
            $q->notDeceased();
        })->with(['pet', 'staff']);

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
        $history = $query->latest('date_administered')->paginate(15)->appends($request->all());

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

        // Find the specific vaccine in inventory to get its REAL batch number
        $inventory = VaccineInventory::where('name', $request->vaccine_name)->first();

        if (!$inventory || $inventory->stock <= 0) {
            return back()->with('error', "Insufficient stock for {$request->vaccine_name}!");
        }

        // Use the actual batch number from  inventory records
        $actualBatchNo = $inventory->batch_no;

        $inventory->decrement('stock', 1);

        // 1. Create Vaccination Record
        Vaccination::create([
            'pet_id' => $id,
            'staff_id' => auth()->id(),
            'vaccine_name' => $request->vaccine_name,
            'date_administered' => $request->date_administered,
            'next_due_date' => $request->next_due_date,
            'batch_no' => $actualBatchNo, // Save the real batch number here
        ]);

        // 2. Update Pet Medical Record
        $pet = Pet::findOrFail($id);
        $pet->update([
            'vaccine_type' => $request->vaccine_name,
            'last_date' => $request->date_administered,
            'next_date' => $request->next_due_date,
        ]);

        // 3. Update the Appointment
        if ($request->appointment_id) {
            $appointment = Appointment::find($request->appointment_id);
            if ($appointment) {
                $appointment->update([
                    'status' => 'Done',
                    'administered_by' => auth()->user()->name,
                    'batch_no' => $actualBatchNo,
                    'vaccine_name' => $request->vaccine_name, // Store the specific vaccine used
                    'next_due_date' => $request->next_due_date,
                ]);
            }
        }

        return redirect()->route('staff.appointments', ['view' => 'completed'])
            ->with('success', "Vaccination logged and appointment completed!");
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
