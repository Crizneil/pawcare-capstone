<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserRequest;
use App\Models\User;
use App\Models\Pet;
use App\Models\Appointment;
use App\Models\ActivityLog;
use App\Models\VaccineInventory;
use App\Models\Vaccination;
use App\Services\SmsService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalPets = Pet::count();
        $totalOwners = User::where('role', 'owner')->count();
        $totalStaff = User::where('role', 'staff')->count();

        // UPDATED: Only count today's appointments if they are APPROVED
        $appointmentsToday = Appointment::whereDate('appointment_date', today())
            ->where('status', 'approved')
            ->count();

        $requests = UserRequest::where('status', 'pending')
            ->with(['pet', 'requester'])
            ->get();

        return view('admin.dashboard', compact(
            'totalPets',
            'totalOwners',
            'totalStaff',
            'appointmentsToday',
            'requests'
        ));
    }

    // --- NEW METHODS FOR SIDEBAR ---

    public function appointments(Request $request)
    {
        // 1. Calculate counts for Summary Cards
        $counts = [
            'today' => Appointment::whereDate('appointment_date', today())->count(),
            'pending' => Appointment::where('status', 'pending')->count(),
            'approved' => Appointment::where('status', 'approved')->count(),
            'completed' => Appointment::whereIn('status', ['completed', 'Done'])->count(),
        ];

        // 2. Fetch Owners for the "New Appointment" Modal dropdown
        $owners = User::where('role', 'owner')->get();

        // 3. Handle the Table Query & Filters
        $query = Appointment::with('user');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->from && $request->to) {
            $query->whereBetween('appointment_date', [$request->from, $request->to]);
        }

        if ($request->owner) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->owner . '%');
            });
        }

        if ($request->pet) {
            $query->where('pet_name', 'like', '%' . $request->pet . '%');
        }

        $appointments = $query->latest()->paginate(10);

        // Pass everything to the view
        return view('admin.appointments', compact('appointments', 'counts', 'owners'));
    }


    public function approve($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->update(['status' => 'approved']);

        ActivityLog::record(
            'APPROVE',
            'Approved appointment for pet: ' . $appointment->pet_name
        );

        // Send SMS Notification
        $user = $appointment->user;
        if ($user && $user->phone) {
            $msg = "Great news! Your appointment for {$appointment->pet_name} on " .
                \Carbon\Carbon::parse($appointment->appointment_date)->format('M d') .
                " has been APPROVED. See you there! - PawCare";
            SmsService::send($user->phone, $msg);
        }

        return back()->with('success', 'Appointment Approved!');
    }

    public function createAppointment()
    {
        // Fetch owners so the dropdown menu in your form actually works
        $owners = User::where('role', 'owner')->get();

        return view('admin.appointment-create', compact('owners'));
    }

    public function reject(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason // Optional: add this column to your migration
        ]);

        return back()->with('success', 'Appointment rejected successfully.');
    }

    public function reschedule(Request $request, $id)
    {
        $request->validate([
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required',
        ]);

        $appointment = Appointment::findOrFail($id);
        $appointment->update([
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'status' => 'approved', // Automatically approve if rescheduled by admin
        ]);

        return back()->with('success', 'Appointment rescheduled successfully.');
    }

    public function cancel($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->update(['status' => 'cancelled']);

        return back()->with('success', 'Appointment cancelled.');
    }
    public function owners()
    {
        $owners = User::where('role', 'owner')->latest()->paginate(10);
        return view('admin.owners', compact('owners'));
    }

    public function logs(Request $request)
    {
        $search = $request->input('search');
        $view = $request->input('view', 'active'); // 'active' or 'archived'

        $query = ActivityLog::with('user');

        // Search Logic
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%")
                            ->orWhere('role', 'like', "%{$search}%");
                    });
            });
        }

        // Toggle between Active and Archived (Soft Deleted)
        if ($view === 'archived') {
            $logs = $query->onlyTrashed()->latest()->paginate(10);
        } else {
            $logs = $query->latest()->paginate(10);
        }

        return view('admin.logs', compact('logs', 'view', 'search'));
    }

    // Method to Archive (Soft Delete)
    public function archiveLogs()
    {
        ActivityLog::whereNotNull('id')->delete();
        return back()->with('success', 'Logs moved to archive.');
    }

    public function profile()
    {
        $admin = Auth::user();
        return view('admin.profile', compact('admin'));
    }
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'house_number' => 'nullable|string|max:255',
            'street' => 'nullable|string|max:255',
            'barangay' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'province' => 'required|string|max:255',
        ]);

        $data = $request->all();

        // Handle File Upload
        if ($request->hasFile('profile_image')) {
            // Delete old image if it exists
            if ($user->profile_image) {
                Storage::delete('public/' . $user->profile_image);
            }

            $path = $request->file('profile_image')->store('profiles', 'public');
            $data['profile_image'] = $path;
        }

        $user->update($data);

        return back()->with('success', 'Profile updated successfully!');
    }

    // Restore a single log
    public function restoreLog($id)
    {
        $log = ActivityLog::onlyTrashed()->findOrFail($id);
        $log->restore();

        return back()->with('success', 'Log restored successfully!');
    }

    // Restore all archived logs at once
    public function restoreAllLogs()
    {
        ActivityLog::onlyTrashed()->restore();

        return back()->with('success', 'All logs have been restored.');
    }

    // --- EXISTING METHODS ---

    public function petRecords()
    {
        $pets = Pet::with('user')->latest()->paginate(10);
        return view('admin.pet-records', compact('pets'));
    }

    public function searchPet(Request $request)
    {
        $request->validate([
            'search' => 'required|string',
        ]);

        $search = $request->search;
        $pet = Pet::where('pet_id', $search)
            ->orWhere('unique_id', $search)
            ->orWhere('name', 'like', "%{$search}%")
            ->first();

        if (!$pet) {
            return back()->with('error', 'Pet not found. Please check the ID or QR scan again.');
        }

        // Redirect to the Pet Records page but maybe with a filter or just highlight?
        // Actually, the UI suggests it redirects to pet medical records.
        // Let's redirect to pet records with a search parameter or find a way to show the modal.
        return redirect()->route('admin.pet-records', ['search' => $pet->unique_id]);
    }
    // --- ADD THESE METHODS FOR PET MANAGEMENT ---

    /**
     * Update the specified pet record
     */
    public function updatePet(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'breed' => 'nullable|string|max:255',
        ]);

        $pet = Pet::findOrFail($id);

        // Store old name for logging
        $oldName = $pet->name;

        $pet->update([
            'name' => $request->name,
            'breed' => $request->breed,
        ]);

        ActivityLog::record(
            'UPDATE',
            "Updated pet record: Changed {$oldName} info."
        );

        return back()->with('success', 'Pet record updated successfully!');
    }

    /**
     * Remove the specified pet record
     */
    public function destroyPet($id)
    {
        $pet = Pet::findOrFail($id);
        $petName = $pet->name;

        ActivityLog::record(
            'DELETE',
            'Deleted pet record for: ' . $petName
        );

        $pet->delete();

        return back()->with('success', "Record for {$petName} has been removed.");
    }

    public function enroll(Request $request)
    {
        $request->validate([
            'owner_name' => 'required|string|max:255',
            'owner_email' => 'required|email',
            'pet_name' => 'required|string|max:255',
            'breed' => 'required|string|max:255',
            'next_date' => 'required|date',
        ]);

        $owner = User::firstOrCreate(
            ['email' => $request->owner_email],
            [
                'name' => $request->owner_name,
                'password' => Hash::make('password'),
                'role' => 'owner',
                'house_number' => 'N/A',
                'street' => 'N/A',
                'barangay' => 'N/A',
                'city' => 'City of Meycauayan',
                'province' => 'Bulacan',
            ]
        );

        $year = date('Y');
        $count = Pet::count() + 1;
        $unique_id = 'PC-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        Pet::create([
            'user_id' => $owner->id,
            'name' => $request->pet_name,
            'species' => 'Dog',
            'breed' => $request->breed,
            'birthdate' => now(),
            'registry_date' => now(),
            'unique_id' => $unique_id,
        ]);

        // Send SMS Notification
        if ($owner->phone) {
            $msg = "Hi {$owner->name}, your pet {$request->pet_name} is now enrolled in PawCare! Unique ID: {$unique_id}. Keep this for your records. - PawCare";
            SmsService::send($owner->phone, $msg);
        }

        return back()->with('success', 'Pet enrolled successfully! ID: ' . $unique_id);
    }

    public function employees()
    {
        $staff = User::where('role', 'staff')->latest()->paginate(10);

        return view('admin.employees', compact('staff'));
    }

    public function storeStaff(Request $request)
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
            'role' => 'staff', // This matches your RoleMiddleware
            'house_number' => 'Clinic',
            'street' => 'MacArthur Hwy',
            'barangay' => 'Clinic Brgy',
            'city' => 'City of Meycauayan',
            'province' => 'Bulacan',
        ]);
        ActivityLog::record(
            'CREATE',
            'Created new staff account: ' . $request->name
        );


        return redirect()->route('admin.employees')->with('success', 'Staff account created successfully!');
    }
    /**
     * Update the specified staff member
     */
    public function updateStaff(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $staff = User::findOrFail($id);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $staff->update($data);

        ActivityLog::record(
            'UPDATE',
            'Updated staff account: ' . $staff->name
        );


        return back()->with('success', 'Staff member updated successfully!');
    }

    /**
     * Remove the specified staff member
     */
    public function destroyStaff($id)
    {
        $staff = User::findOrFail($id);

        if ($staff->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account!');
        }

        ActivityLog::record(
            'DELETE',
            'Deleted staff account: ' . $staff->name
        );

        $staff->delete();

        return back()->with('success', 'Staff account removed successfully.');
    }

    public function storeAppointment(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'pet_name' => 'required|string|max:255',
            'species' => 'required|string',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required',
            'service_type' => 'required|string',
        ]);

        Appointment::create([
            'user_id' => $request->user_id,
            'pet_name' => $request->pet_name,
            'species' => $request->species,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'service_type' => $request->service_type,
            'status' => 'pending', // Default status for new appointments
        ]);

        // Added: Automatic Medical Scheduling Logic for Week 1 (Same as PetController)
        if ($request->service_type === 'week_1') {
            $pet = Pet::where('user_id', $request->user_id)
                ->where('name', $request->pet_name)
                ->first();

            if ($pet) {
                $initialDate = \Carbon\Carbon::parse($request->appointment_date);

                // 1. Deworming #1: +2 weeks
                Vaccination::create([
                    'pet_id' => $pet->id,
                    'vaccine_name' => 'Deworming #1',
                    'date_administered' => $initialDate->copy()->addWeeks(2),
                    'status' => 'Scheduled',
                    'remarks' => 'Automatic follow-up from Week 1 Visit (Admin Created)'
                ]);

                // 2. Deworming #2: +4 weeks
                Vaccination::create([
                    'pet_id' => $pet->id,
                    'vaccine_name' => 'Deworming #2',
                    'date_administered' => $initialDate->copy()->addWeeks(4),
                    'status' => 'Scheduled',
                    'remarks' => 'Automatic follow-up from Week 1 Visit (Admin Created)'
                ]);

                // 3. 5-in-1 Vaccination: +6 weeks
                Vaccination::create([
                    'pet_id' => $pet->id,
                    'vaccine_name' => '5-in-1 Vaccination',
                    'date_administered' => $initialDate->copy()->addWeeks(6),
                    'status' => 'Scheduled',
                    'remarks' => 'Automatic follow-up from Week 1 Visit (Admin Created)'
                ]);
            }
        }

        // Send SMS Notification
        $user = User::find($request->user_id);
        if ($user && $user->phone) {
            $msg = "Hi {$user->name}, an appointment has been scheduled for {$request->pet_name} on " .
                \Carbon\Carbon::parse($request->appointment_date)->format('M d') .
                " via PawCare Admin. - PawCare";
            SmsService::send($user->phone, $msg);
        }

        return back()->with('success', 'New appointment received and is awaiting approval!');
    }

    public function updateVaccine(Request $request, $id)
    {
        $vaccine = VaccineInventory::findOrFail($id);
        $vaccine->update($request->only(['stock', 'batch_no', 'expiry_date']));

        return back()->with('success', "{$vaccine->name} inventory updated.");
    }

    public function destroyVaccine($id)
    {
        $vaccine = VaccineInventory::findOrFail($id);
        $vaccine->delete();

        return back()->with('success', "Vaccine record deleted.");
    }
    public function storeVaccine(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'batch_no' => 'required|string|unique:vaccine_inventories,batch_no',
            'stock' => 'required|integer|min:0',
            'low_stock_threshold' => 'required|integer',
            'expiry_date' => 'required|date',
        ]);

        // Set a default description based on the name if needed
        $validated['description'] = $request->name . " inventory batch.";

        VaccineInventory::create($validated);

        return back()->with('success', 'New vaccine batch added to inventory!');
    }
}
