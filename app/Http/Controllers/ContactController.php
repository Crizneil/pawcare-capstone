<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function contactDeveloper(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string'
        ]);

        // Send the actual email
        try {
            \Illuminate\Support\Facades\Mail::to('crizneils@gmail.com')
                ->send(new \App\Mail\ContactDeveloper($request->name, $request->email, $request->message));

            Log::info("DEV_CONTACT_MAIL: Successfully sent from {$request->email} to crizneils@gmail.com");
        } catch (\Exception $e) {
            Log::error("DEV_CONTACT_MAIL_ERROR: " . $e->getMessage());
            // We still log it as a fallback
            Log::info("DEV_CONTACT_MESSAGE (Fallback Log):");
            Log::info("FROM: {$request->name} ({$request->email})");
            Log::info("MESSAGE: {$request->message}");
        }

        return response()->json([
            'success' => true,
            'message' => 'Message sent to developers! 🐾'
        ]);
    }
}
