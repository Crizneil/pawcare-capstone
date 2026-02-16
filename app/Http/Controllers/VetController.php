<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pet;
use App\Models\Vaccine;

class VetController extends Controller
{
    public function dashboard()
    {
        return view('vet.dashboard');
    }

    public function petRecords(Request $request)
    {
        $query = Pet::with('user');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('unique_id', $search);
        }

        $pets = $query->latest()->paginate(10);
        return view('admin.pet-records', compact('pets'));
    }

    public function vaccines(Request $request)
    {
        // Vets can view vaccines
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

    public function searchPet(Request $request)
    {
        $search = $request->input('search');

        // Extract ID if a full URL was scanned/pasted
        if (filter_var($search, FILTER_VALIDATE_URL) || strpos($search, '/p/') !== false) {
            $parts = explode('/p/', $search);
            $search = end($parts);
        }

        $pet = Pet::where('unique_id', $search)->first();

        if (!$pet) {
            return back()->with('error', 'Pet not found with ID: ' . $search);
        }

        // Redirect to pet records in the admin view but filtered for this pet
        // This allows staff to immediately click "Add Vaccine"
        return redirect()->route('vet.pet-records', ['search' => $pet->unique_id]);
    }
}
