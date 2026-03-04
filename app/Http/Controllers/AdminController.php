<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserRequest;
use App\Models\User;
use App\Models\Pet;
use App\Models\Appointment;
use App\Models\ActivityLog;
use App\Models\VaccineInventory;
use App\Mail\AppointmentReminder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\WelcomeEmail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalPets = Pet::notDeceased()->count();
        $totalOwners = User::where('role', 'owner')->count();
        $totalStaff = User::where('role', 'staff')->count();

        // UPDATED: Only count today's appointments if they are APPROVED
        $appointmentsToday = Appointment::whereDate('appointment_date', today())
            ->where('status', 'approved')
            ->count();

        $requests = \App\Models\UserRequest::where('status', 'pending')
            ->with(['pet', 'requester'])
            ->get();

        // Fetch low stock vaccines to display an alert
        $lowStockVaccines = \App\Models\VaccineInventory::whereColumn('stock', '<=', 'low_stock_threshold')->get();

        return view('admin.dashboard', compact(
            'totalPets',
            'totalOwners',
            'totalStaff',
            'appointmentsToday',
            'requests',
            'lowStockVaccines'
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
            // Special case: "completed" filter should include both "completed" and "Done"
            if ($request->status === 'completed') {
                $query->whereIn('status', ['completed', 'Done']);
            } else {
                $query->where('status', $request->status);
            }
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

    /**
     * API: Get all appointments for the FullCalendar master view.
     */
    public function getAppointmentsApi(Request $request)
    {
        // IMPORTANT: Cancelled appointments should not appear on any calendar view
        // (keep them available in the table/list views via other endpoints).
        $appointments = Appointment::with(['user', 'pet'])
            ->whereNotIn('status', ['cancelled', 'Cancelled'])
            ->get();

        $events = $appointments->map(function ($appt) {
            // Colors based on status
            $colors = [
                'pending' => '#fd7e14', // Orange
                'approved' => '#0d6efd', // Blue
                'completed' => '#198754', // Green
                'Done' => '#198754',
                'cancelled' => '#dc3545', // Red
                'rejected' => '#6c757d', // Gray
            ];

            return [
                'id' => $appt->id,
                'title' => $appt->pet_name . ' (' . ucfirst($appt->service_type) . ')',
                'start' => $appt->appointment_date . 'T' . $appt->appointment_time,
                'backgroundColor' => $colors[$appt->status] ?? '#6c757d',
                'borderColor' => $colors[$appt->status] ?? '#6c757d',
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'owner_name' => $appt->user->name ?? 'Unknown',
                    'owner_phone' => $appt->user->phone ?? 'Unknown',
                    'owner_address' => $appt->address ?? 'Not Provided',
                    'species' => ucfirst($appt->species),
                    'status' => ucfirst($appt->status)
                ]
            ];
        });

        return response()->json($events);
    }

    /**
     * API: Update appointment date/time from Master Calendar Drag-and-Drop
     */
    public function updateDragAndDrop(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Invalid date/time format.'], 400);
        }

        $appointment = Appointment::findOrFail($id);

        if (in_array(strtolower($appointment->status), ['completed', 'done', 'cancelled', 'rejected'])) {
            return response()->json(['success' => false, 'message' => 'Cannot reschedule a closed appointment.'], 403);
        }

        $oldDate = Carbon::parse($appointment->appointment_date)->format('M d, Y') . ' at ' . Carbon::parse($appointment->appointment_time)->format('h:i A');
        $newDate = Carbon::parse($request->appointment_date)->format('M d, Y') . ' at ' . Carbon::parse($request->appointment_time)->format('h:i A');

        $appointment->update([
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
        ]);

        ActivityLog::record(
            'RESCHEDULE_APPOINTMENT',
            "Admin rescheduled Appointment #APT-{$appointment->id} from {$oldDate} to {$newDate} via Calendar Drag-and-Drop."
        );

        return response()->json(['success' => true, 'message' => 'Appointment successfully rescheduled.']);
    }

    public function approve($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->update(['status' => 'approved']);

        ActivityLog::record('APPROVE', 'Approved appointment for pet: ' . $appointment->pet_name);

        // Send approval email
        Mail::to($appointment->user->email)
            ->send(new AppointmentReminder($appointment, 'approved'));

        return back()->with('success', 'Appointment Approved and owner notified!');
    }

    public function createAppointment()
    {
        // Fetch owners so the dropdown menu in your form actually works
        $owners = User::where('role', 'owner')->get();

        return view('admin.appointment-create', compact('owners'));
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        $appointment = Appointment::findOrFail($id);
        $appointment->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason
        ]);

        ActivityLog::record('REJECT', "Rejected appointment #{$id} for {$appointment->pet_name}. Reason: {$request->rejection_reason}");

        // Send rejection email
        Mail::to($appointment->user->email)
            ->send(new AppointmentReminder($appointment, 'rejected'));

        return back()->with('success', 'Appointment rejected and owner notified.');
    }

    public function reschedule(Request $request, $id)
    {
        $request->validate([
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required',
        ]);

        $appointment = Appointment::findOrFail($id);
        $oldDate = $appointment->appointment_date;

        $appointment->update([
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'status' => 'rescheduled',
        ]);

        ActivityLog::record('RESCHEDULE', "Moved appointment #{$id} from {$oldDate} to {$request->appointment_date}");

        // Send reschedule email
        Mail::to($appointment->user->email)
            ->send(new AppointmentReminder($appointment, 'rescheduled'));

        return back()->with('success', 'Appointment rescheduled and owner notified.');
    }

    public function cancel($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->update(['status' => 'cancelled']);

        return back()->with('success', 'Appointment cancelled.');
    }

    public function markDone($id)
    {
        $appointment = Appointment::with('user')->findOrFail($id);

        $current = strtolower((string) $appointment->status);
        if (in_array($current, ['done', 'completed', 'cancelled', 'rejected'], true)) {
            return back()->with('error', 'This appointment is already closed.');
        }

        // Mark as completed (use "Done" to match existing staff/admin logic)
        $appointment->update(['status' => 'Done']);

        ActivityLog::record(
            'MARK_DONE',
            'Marked appointment as Done for pet: ' . $appointment->pet_name
        );

        return back()->with('success', 'Appointment marked as Done.');
    }
    public function owners()
    {
        $owners = User::where('role', 'owner')->latest()->paginate(10);
        return view('admin.owners', compact('owners'));
    }

    public function storeOwner(Request $request)
    {
        // 1. Validate under the "One-Account Policy"
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'valid_id_number' => 'required|string|unique:users', // Enforce real ID uniqueness
            'phone' => 'required|numeric|digits:11|unique:users', // Enforce unique mobile number
            'address' => 'required|string',
            'gender' => 'required|string|in:Male,Female',
        ], [
            'email.unique' => 'An account with this email address already exists. Only one account is allowed per owner.',
            'valid_id_number.unique' => 'This Valid ID Number is already registered to another owner.',
            'phone.unique' => 'An account with this mobile number already exists. Only one account is allowed per owner.',
        ]);

        // 2. Generate a secure random password (e.g., 8 characters)
        $rawPassword = Str::random(8);

        // 3. Create the Owner User
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'valid_id_number' => $request->valid_id_number,
            'password' => Hash::make($rawPassword),
            'phone' => $request->phone,
            'address' => $request->address,
            'gender' => $request->gender,
            'role' => 'owner',
            // Default generic values
            'city' => 'City of Meycauayan',
            'province' => 'Bulacan',
            'house_number' => 'N/A',
            'street' => 'N/A',
            'barangay' => 'N/A',
        ]);

        // 4. Record Activity
        ActivityLog::record(
            'CREATE_OWNER',
            'Admin successfully created a new owner account for: ' . $user->name
        );

        // 5. Send Automated Welcome Email to the Owner
        try {
            Mail::to($user->email)->send(new WelcomeEmail($user, $rawPassword));
        } catch (\Exception $e) {
            // Failsafe in case email fails to send (optional based on your environment)
            return back()->with('error', 'Owner registered, but there was an issue sending the welcome email: ' . $e->getMessage());
        }

        return back()->with('success', 'New owner account successfully registered and Welcome Email sent!');
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
            'phone' => 'nullable|string|max:11',
            'gender' => 'nullable|string',
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

    public function petRecords(\Illuminate\Http\Request $request)
    {
        $view = $request->input('view', 'active'); // 'active' or 'archived'
        $query = Pet::with('user');

        if ($request->filled('pet_id')) {
            $query->withTrashed()->where(function ($q) use ($request) {
                $q->where('id', $request->pet_id)
                    ->orWhere('pet_id', $request->pet_id);
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

            // 2. Regular Text Search Match
            if ($request->filled('general_search')) {
                $search = $request->general_search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('breed', 'like', "%{$search}%")
                        ->orWhere('owner', 'like', "%{$search}%");
                });
            }
        }

        $pets = $query->latest()->paginate(10)->appends($request->all());
        $owners = User::where('role', 'owner')->get(); // Fetch owners for Add Pet Modal
        return view('admin.pet-records', compact('pets', 'owners', 'view'));
    }

    /**
     * Restore a pet that was marked as DECEASED (Status Only)
     */
    public function restoreDeceasedPet($id)
    {
        $pet = Pet::findOrFail($id);

        if (!in_array($pet->status, ['DECEASED', 'INACTIVE'])) {
            return back()->with('error', 'This pet is not marked as deceased or inactive.');
        }

        $pet->update(['status' => 'Verified']); // Or ACTIVE

        ActivityLog::record(
            'RESTORE',
            "Restored inactive/deceased pet record for: {$pet->name}"
        );

        return back()->with('success', "Pet record for {$pet->name} has been restored to active status.");
    }
    // --- ADD THESE METHODS FOR PET MANAGEMENT ---

    /**
     * Store a newly created pet record (Admin-Only)
     */
    public function storePet(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'species' => 'required|string|in:Dog,Cat',
            'breed' => 'nullable|string|max:255',
            'birthdate' => 'required|date|before_or_equal:today',
        ]);

        $year = date('Y');
        $count = Pet::count() + 1;
        $unique_id = 'PC-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        $pet = Pet::create([
            'user_id' => $request->user_id,
            'pet_id' => $unique_id,
            'name' => $request->name,
            'species' => $request->species,
            'breed' => $request->breed ?? 'Unknown',
            'birthday' => $request->birthdate,
            'gender' => 'Unknown', // Need to pass default gender to satisfy schema
            'owner' => User::find($request->user_id)->name ?? 'Unknown',
            'status' => 'Verified', // Pets added by Admin are automatically verified
        ]);

        ActivityLog::record(
            'CREATE_PET',
            "Admin successfully registered a new pet ({$pet->name}) for owner ID: {$pet->user_id}"
        );

        return back()->with('success', 'Pet successfully registered! Pet ID: ' . $unique_id);
    }

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
            'status' => $request->status ?? $pet->status, // Fallback if unmodified
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
            'pet_id' => $unique_id,
            'name' => $request->pet_name,
            'species' => 'Dog',
            'breed' => $request->breed,
            'birthday' => now()->toDateString(),
            'gender' => 'Unknown',
            'owner' => $owner->name,
            'next_date' => $request->next_date,
            'status' => 'Verified',
        ]);

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
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'pet_name' => 'required|string|max:255',
                'species' => 'required|string',
                'gender' => 'required|string',
                'breed' => 'required|string',
                'other_breed' => 'nullable|string|required_if:breed,Other',
                'birthday' => 'required|date',
                'appointment_date' => 'required|date',
                'appointment_time' => 'required',
                'service_type' => 'required|string',
            ]);

            $user = User::findOrFail($request->user_id);

            // Try to find an existing pet for this user with the same name
            $pet = Pet::where('user_id', $user->id)
                ->where('name', $request->pet_name)
                ->first();

            // If pet doesn't exist, create it with full details provided
            if (!$pet) {
                $finalBreed = ($request->breed === 'Other') ? $request->other_breed : $request->breed;

                $pet = Pet::create([
                    'user_id' => $user->id,
                    'pet_id' => 'ADMIN-' . strtoupper(substr(uniqid(), -5)),
                    'name' => $request->pet_name,
                    'species' => $request->species,
                    'gender' => $request->gender,
                    'breed' => $finalBreed,
                    'birthday' => $request->birthday,
                    'owner' => $user->name,
                    'status' => 'ACTIVE',
                ]);
            }

            // Standardize time format for database
            $formattedTime = date('H:i:s', strtotime($request->appointment_time));

            // Double Booking Prevention Check
            return \Illuminate\Support\Facades\DB::transaction(function () use ($request, $user, $pet, $formattedTime) {
                $existing = Appointment::where('appointment_date', $request->appointment_date)
                    ->where('appointment_time', $formattedTime)
                    ->whereNotIn('status', ['cancelled', 'rejected'])
                    ->lockForUpdate()
                    ->exists();

                if ($existing) {
                    return back()->withErrors(['appointment_time' => 'Sorry, this time slot has just been booked by someone else. Please choose another time.'])->withInput();
                }

                Appointment::create([
                    'user_id' => $user->id,
                    'pet_id' => $pet->id,
                    'pet_name' => $pet->name,
                    'species' => $pet->species,
                    'appointment_date' => $request->appointment_date,
                    'appointment_time' => $formattedTime,
                    'service_type' => $request->service_type,
                    'status' => 'approved', // Admin bookings are auto-approved
                ]);

                return back()->with('success', 'Appointment scheduled successfully!');
            });
        } catch (\Exception $e) {
            // LOG THE REAL ERROR to bypass the failing error renderer
            @file_put_contents(base_path('storage/logs/appt_error.log'), "[" . date('Y-m-d H:i:s') . "] " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n\n", FILE_APPEND);
            return back()->withErrors(['error' => 'An error occurred while saving the appointment. Please check the logs.'])->withInput();
        }
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
    public function archive(Request $request)
    {
        $tab = $request->input('tab', 'pets');
        $search = $request->input('search');

        $data = [];
        if ($tab === 'pets') {
            $query = Pet::onlyTrashed()->with('user');
            if ($search)
                $query->where('name', 'like', "%{$search}%");
            $data = $query->latest()->paginate(10);
        } elseif ($tab === 'staff') {
            $query = User::onlyTrashed()->where('role', 'staff');
            if ($search)
                $query->where('name', 'like', "%{$search}%");
            $data = $query->latest()->paginate(10);
        } elseif ($tab === 'vaccines') {
            $query = VaccineInventory::onlyTrashed();
            if ($search)
                $query->where('name', 'like', "%{$search}%");
            $data = $query->latest()->paginate(10);
        }

        return view('admin.archive', compact('data', 'tab', 'search'));
    }

    public function restorePet($id)
    {
        $pet = Pet::onlyTrashed()->findOrFail($id);
        $pet->restore();

        ActivityLog::record(
            'RESTORE',
            "Restored pet record for: " . $pet->name
        );

        return back()->with('success', "Pet record for {$pet->name} restored.");
    }

    public function forceDeletePet($id)
    {
        $pet = Pet::withTrashed()->findOrFail($id);
        $name = $pet->name;
        $pet->forceDelete();

        ActivityLog::record(
            'PERMANENT_DELETE',
            "Permanently deleted pet record for: " . $name
        );

        return back()->with('success', "Pet record for {$name} deleted permanently.");
    }

    public function restoreStaff($id)
    {
        $staff = User::onlyTrashed()->findOrFail($id);
        $staff->restore();

        ActivityLog::record(
            'RESTORE',
            "Restored staff account: " . $staff->name
        );

        return back()->with('success', "Staff account for {$staff->name} restored.");
    }

    public function forceDeleteStaff($id)
    {
        $staff = User::onlyTrashed()->findOrFail($id);
        $name = $staff->name;
        $staff->forceDelete();

        ActivityLog::record(
            'PERMANENT_DELETE',
            "Permanently deleted staff account: " . $name
        );

        return back()->with('success', "Staff account for {$name} deleted permanently.");
    }

    public function restoreVaccine($id)
    {
        $vaccine = VaccineInventory::onlyTrashed()->findOrFail($id);
        $vaccine->restore();

        ActivityLog::record(
            'RESTORE',
            "Restored vaccine record: " . $vaccine->name
        );

        return back()->with('success', "Vaccine record for {$vaccine->name} restored.");
    }

    public function forceDeleteVaccine($id)
    {
        $vaccine = VaccineInventory::onlyTrashed()->findOrFail($id);
        $name = $vaccine->name;
        $vaccine->forceDelete();

        ActivityLog::record(
            'PERMANENT_DELETE',
            "Permanently deleted vaccine record: " . $name
        );

        return back()->with('success', "Vaccine record for {$name} deleted permanently.");
    }

    public function sendEmailReminder($appointment)
    {
        $details = [
            'name' => $appointment->user->name,
            'pet' => $appointment->pet_name,
            'date' => $appointment->appointment_date,
        ];

        Mail::to($appointment->user->email)->send(new AppointmentReminder($details));

        return back()->with('success', 'Email reminder sent!');
    }
}
