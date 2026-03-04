<?php

namespace App\Http\Controllers;
use App\Models\Pet;
use Illuminate\Http\Request;

class PublicPetController extends Controller
{
    public function show($pet_id)
    {
        // Find pet by its custom Registry ID (e.g., PC-12345)
        $pet = Pet::where('pet_id', $pet_id)->with(['user', 'vaccinations'])->firstOrFail();

        return view('public.pet_verify', compact('pet'));
    }
}
