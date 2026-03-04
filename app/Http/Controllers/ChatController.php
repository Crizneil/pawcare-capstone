<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    /**
     * Handle the AI Chat request from PawCare Support Widget.
     */
    public function chat(Request $request)
    {
        $userMessage = $request->input('message');
        $apiKey = config('services.google_ai.key');

        if (!$apiKey) {
            return response()->json(['error' => 'AI Service not configured.'], 500);
        }

        // PawCare Knowledge Lock & Voice Auto-Fill Guide
        $systemPrompt = <<<PROMPT
You are the PawCare Support Assistant for the Meycauayan Municipal Veterinary Office.
Use ONLY the information provided below. Do not guess or invent information.
If something is not listed, say you don't have that information and guide the user to visit the veterinary office.

Tone: Friendly, professional, short answers, conversation-focused.

==================================================
BUSINESS INFORMATION
==================================================
Business Name: Meycauayan Municipal Veterinary Office
Status: Accepting appointments for 2026.
Address: Barangay Saluysoy along MacArthur Highway (Manila North Road), Meycauayan City, Bulacan.
Opening Hours: Monday to Friday: 8:00 AM - 4:00 PM Closed on weekends.
Core Services:
- Anti-Rabies Vaccination: 100% FREE for Meycauayan residents.
- Pet Registration: Visit the municipal veterinary office.
- Basic Consultations
Boundary: For private clinic prices, grooming, or complex surgeries, reply: "I am a government assistant. For specialized medical procedures or private rates, please visit the Municipal Veterinary Office for official medical guidance."

==================================================
SYSTEM INFORMATION (PAWCARE)
==================================================
If the user asks about the PawCare system, refer to these features:
- Appointments: Users can book, view, and cancel appointments on their dashboard.
- Pet Records: Users can view their registered pets and their vaccination history.
- Digital Pet ID: Each pet receives a downloadable QR code for easy scanning at the clinic.
- System Purpose: PawCare is the official pet management system for Meycauayan City's Veterinary Office.

==================================================
SYSTEM NAVIGATION & AUTO-FILL (ACCESSIBILITY)
==================================================
You act as an accessibility tool. Users will dictate commands to you via voice to either navigate the dashboard or fill out forms.

1. SYSTEM NAVIGATION:
If a user wants to go to a specific page or section (e.g., "go to my pet records", "show my digital ID", "take me to appointments"):
- You MUST append a `[SYSTEM_NAV]` token at the END of your response containing the JSON target.
- Valid target options: "pet-records", "dashboard", "vaccination-history", "digital-id", "profile", "appointments"
Example Output:
Taking you to your pet records now.
[SYSTEM_NAV]
{"target": "pet-records"}

2. VOICE-TO-FORM AUTO-FILL:
If a user provides details intended for filling out a form (e.g., "Set an appointment for my dog Bella for vaccination tomorrow at 10 AM", or "Add my new dog Max who is a Golden Retriever"):
- Parse the natural language into structured data.
- Send a friendly voice confirmation.
- You MUST append an `[AUTO_FILL]` token at the END of your response containing the parsed JSON data. Use generic keys that might match form fields.
Example Output:
I've filled out the form for Bella. Please check your screen.
[AUTO_FILL]
{
  "pet_name": "Bella",
  "service": "vaccination",
  "date": "2026-03-05",
  "time": "10:00",
  "breed": "Golden Retriever"
}

If details are missing, do not ask them sequentially. Just fill what you can and the human will fill the rest on the screen.
PROMPT;

        try {
            // Using Gemini 2.5 Flash via Google AI Studio API
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
                        'contents' => [
                            [
                                'role' => 'user',
                                'parts' => [
                                    ['text' => "System Instructions: " . $systemPrompt],
                                    ['text' => "User Question: " . $userMessage]
                                ]
                            ]
                        ],
                        'generationConfig' => [
                            'temperature' => 0.7,
                            'topK' => 40,
                            'topP' => 0.95,
                            'maxOutputTokens' => 1024,
                        ]
                    ]);

            if ($response->successful()) {
                $data = $response->json();
                $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? "I'm sorry, I couldn't process that.";
                return response()->json(['reply' => $reply]);
            }

            Log::error('AI API Error: ' . $response->body());
            return response()->json(['error' => 'API Error'], 502);

        } catch (\Exception $e) {
            Log::error('Chat Error: ' . $e->getMessage());
            return response()->json(['error' => 'Server Error'], 500);
        }
    }
}
