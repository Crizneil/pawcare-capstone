<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Appointment;
use App\Models\VaccineInventory;
use App\Models\Vaccination;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminReportController extends Controller
{
    public function appointmentReport(Request $request)
    {
        // 1. Date-Range Filtering Logic
        $range = $request->query('range', 'daily');

        $startDate = match ($range) {
            'weekly' => now()->startOfWeek(),
            'monthly' => now()->startOfMonth(),
            default => now()->startOfDay()
        };
        $endDate = match ($range) {
            'weekly' => now()->endOfWeek(),
            'monthly' => now()->endOfMonth(),
            default => now()->endOfDay()
        };

        // 2. Build the Query with Relationships
        $query = Appointment::with(['user', 'pet'])
            ->whereBetween('appointment_date', [$startDate, $endDate]);

        // 3. Status Summary Logic (Important for your dashboard/report)
        $summary = [
            'total' => (clone $query)->count(),
            'completed' => (clone $query)->whereIn('status', ['completed', 'Done'])->count(),
            'missed' => (clone $query)->where('status', 'missed')->count(),
            'cancelled' => (clone $query)->where('status', 'cancelled')->count(),
        ];

        // 4. Get the List
        $appointments = (clone $query)->orderBy('appointment_date', 'asc')->get();

        // 5. Handle PDF Download
        if ($request->has('pdf')) {
            $pdf = Pdf::loadView('admin.reports.appointment_pdf', [
                'data' => $appointments,
                'summary' => $summary,
                'range' => $range,
                'isPdf' => true // This hides the buttons in the final PDF
            ]);

            $pdf->setPaper('a4', 'portrait');
            return $pdf->stream("Appointment_Report_{$range}.pdf");
        }

        // 6. Default Web View
        return view('admin.reports.appointment_pdf', [
            'data' => $appointments,
            'summary' => $summary,
            'range' => $range,
            'isPdf' => false
        ]);
    }

    public function inventoryReport(Request $request)
    {
        // 1. Stock Movement & Low Stock Highlight Logic
        $vaccines = VaccineInventory::all();

        foreach ($vaccines as $vax) {
            // Dynamic flag to style "Bold Red Text" in the UI
            $vax->is_low_stock = $vax->stock <= $vax->low_stock_threshold;
        }

        // 2. Expiry Tracking (Items expiring within 30 Days)
        $expiringVaccines = VaccineInventory::whereNotNull('expiry_date')
            ->where('expiry_date', '<=', now()->addDays(30))
            ->get();

        // 3. Usage Logs (Items administered within exactly THIS Month)
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        $usageLogs = Vaccination::whereBetween('date_administered', [$startOfMonth, $endOfMonth])
            ->select('vaccine_name', \DB::raw('count(*) as total_used'))
            ->groupBy('vaccine_name')
            ->get();

        return view('admin.reports.inventory', compact('vaccines', 'expiringVaccines', 'usageLogs'));
    }
    public function generateVaccineReport(Request $request)
    {
        $type = $request->query('type', 'all');
        $query = VaccineInventory::query();

        // 1. Filtering logic
        if ($type == 'low_stock') {
            $query->whereColumn('stock', '<=', 'low_stock_threshold');
        } elseif ($type == 'expiring') {
            // Items expiring in the next 90 days
            $query->where('expiry_date', '<=', now()->addDays(90))
                  ->where('expiry_date', '>', now());
        }

        $data = $query->orderBy('name', 'asc')->get();

        // 2. Enhanced PDF Download Logic
        if ($request->has('download')) {
            // We pass 'isPdf' => true so the Blade view can hide buttons and fix the footer
            $pdf = Pdf::loadView('admin.reports.vaccine_pdf', [
                'data' => $data,
                'type' => $type,
                'isPdf' => true
            ]);

            // Set paper to A4 for official municipal records
            $pdf->setPaper('a4', 'portrait');

            return $pdf->download('Vaccine_Inventory_Report_'.now()->format('M_d_Y').'.pdf');
        }

        // Default: Web view (isPdf is false)
        return view('admin.reports.vaccine_pdf', [
            'data' => $data,
            'type' => $type,
            'isPdf' => false
        ]);
    }
}
