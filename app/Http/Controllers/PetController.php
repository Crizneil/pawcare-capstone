<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\Appointment;
use App\Models\Vaccination;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PetController extends Controller
{
    public function index()
    {
        $pets = Pet::latest()->get();
        // Points to resources/views/admin/pet-records.blade.php
        return view('admin.pet-records', compact('pets'));
    }
    /**
     * PET OWNER VIEW: Only sees pets belonging to the logged-in user
     */
    public function ownerDashboard()
    {
        // Get only pets of logged in owner
        $pets = Pet::where('user_id', Auth::id())->latest()->get();

        // Pet Count
        $petCount = $pets->count();

        // Upcoming Appointment (sample for now)
        $nextAppointment = 'Feb 25, 2026';
        $appointmentStatus = 'Confirmed';

        // Upcoming Vaccine Due
        $nextVaccine = optional(
            $pets->sortBy('next_date')->first()
        )->next_date;

        // Reminder Alert Logic
        $vaccineReminder = null;

        if ($nextVaccine) {
            $daysRemaining = now()->diffInDays($nextVaccine, false);

            if ($daysRemaining <= 7 && $daysRemaining >= 0) {
                $vaccineReminder = 'Rabies Vaccine due in ' . $daysRemaining . ' day(s)';
            }
        }

        // Clinic Announcement
        $announcement = 'Free Anti-Rabies Vaccination this Saturday!';

        return view('pet-owner.dashboard', compact(
            'pets',
            'petCount',
            'nextAppointment',
            'appointmentStatus',
            'nextVaccine',
            'vaccineReminder',
            'announcement'
        ));
    }

    public function appointments()
    {
        // Fetch the owner's appointments
        $appointments = Appointment::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('pet-owner.appointments', compact('appointments'));
    }
    public function cancelAppointment($id)
    {
        // Find appointment and ensure it belongs to the logged-in user
        $appointment = Appointment::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Check if it's already completed or already cancelled
        if ($appointment->status === 'completed' || $appointment->status === 'Done') {
            return back()->with('error', 'Cannot cancel a completed appointment.');
        }

        $appointment->update(['status' => 'cancelled']);

        return back()->with('success', 'Appointment cancelled successfully.');
    }

    public function book(Request $request)
    {
        $request->validate([
            'pet_id' => 'required',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
            'service_type' => 'required'
        ]);

        $pet = Pet::findOrFail($request->pet_id);

        Appointment::create([
            'user_id' => auth()->id(),
            'pet_id' => $pet->id,
            'pet_name' => $pet->name,
            'species' => $pet->species,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'service_type' => $request->service_type,
            'status' => 'pending'
        ]);

        // Send SMS Notification
        $user = auth()->user();
        if ($user->phone) {
            $msg = "Hi {$user->name}, your appointment request for {$pet->name} on " .
                \Carbon\Carbon::parse($request->appointment_date)->format('M d') .
                " has been received. Please wait for approval. - PawCare";
            SmsService::send($user->phone, $msg);
        }

        // Automatic Medical Scheduling Logic for Week 1
        if ($request->service_type === 'week_1') {
            $initialDate = \Carbon\Carbon::parse($request->appointment_date);

            // 1. Deworming #1: +2 weeks
            Vaccination::create([
                'pet_id' => $pet->id,
                'vaccine_name' => 'Deworming #1',
                'date_administered' => $initialDate->copy()->addWeeks(2),
                'status' => 'Scheduled',
                'remarks' => 'Automatic follow-up from Week 1 Visit'
            ]);

            // 2. Deworming #2: +4 weeks
            Vaccination::create([
                'pet_id' => $pet->id,
                'vaccine_name' => 'Deworming #2',
                'date_administered' => $initialDate->copy()->addWeeks(4),
                'status' => 'Scheduled',
                'remarks' => 'Automatic follow-up from Week 1 Visit'
            ]);

            // 3. 5-in-1 Vaccination: +6 weeks
            Vaccination::create([
                'pet_id' => $pet->id,
                'vaccine_name' => '5-in-1 Vaccination',
                'date_administered' => $initialDate->copy()->addWeeks(6),
                'status' => 'Scheduled',
                'remarks' => 'Automatic follow-up from Week 1 Visit'
            ]);
        }

        return redirect()->route('pet-owner.appointments')->with('success', 'Appointment requested!');
    }
    public function petOwners()
    {
        return view('admin.pet-owners');
    }

    public function vaccinations()
    {
        return view('admin.vaccinations');
    }

    public function settings()
    {
        return view('admin.settings');
    }

    public function store(Request $request)
    {
        $pet = Pet::create([
            'pet_id' => 'PC-2026-' . rand(1000, 9999),
            'user_id' => Auth::id(),
            'name' => $request->name,
            'gender' => $request->gender,
            'species' => $request->species,
            'birthday' => $request->birthday,
            'breed' => $request->breed,
            'owner' => Auth::user()->name,
            'last_date' => now(),
            'next_date' => $request->next_date,
            'vaccine_type' => $request->vaccine_type ?? 'None',
            'image_url' => $request->image_url,
            'status' => 'ACTIVE'
        ]);

        // Send SMS Notification
        $user = Auth::user();
        if ($user->phone) {
            $msg = "Welcome to the pack! {$pet->name} has been registered. Unique ID: {$pet->pet_id}. Keep this for your records! - PawCare";
            SmsService::send($user->phone, $msg);
        }

        return redirect()->route('pet-owner.pet-records')->with('success', 'Pet registered successfully!');
    }

    public function destroy($id)
    {
        Pet::findOrFail($id)->delete();
        return response()->json(['message' => 'Deleted']);
    }
    public function petRecords()
    {
        // Fetch only pets belonging to the logged-in user
        $pets = Pet::where('user_id', Auth::id())->latest()->get();

        // Points to resources/views/pet-owner/pet-records.blade.php
        return view('pet-owner.pet-records', compact('pets'));
    }

    /**
     * Single View: Shows the printable Digital ID Card for one specific pet
     */
    public function showDigitalId($id)
    {
        // Find pet by ID but ensure it belongs to the logged-in user for security
        $pet = Pet::where('id', $id)
            ->where('user_id', Auth::id())
            ->with('vaccinations') // Assuming you have this relationship
            ->firstOrFail();

        // Points to resources/views/pet-owner/digital-id.blade.php
        return view('pet-owner.digital-id', compact('pet'));
    }
    public function publicProfile($pet_id)
    {
        // Search by the custom pet_id (e.g., PC-2026-8929) not the database auto-increment ID
        $pet = Pet::where('pet_id', $pet_id)->firstOrFail();

        return view('public.pet-verify', compact('pet'));
    }

    /**
     * Profile page for the owner
     */
    public function profile()
    {
        return view('pet-owner.profile', ['user' => Auth::user()]);
    }

}
