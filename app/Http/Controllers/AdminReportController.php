<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Appointment;
use App\Models\VaccineInventory;
use App\Models\Vaccination;

class AdminReportController extends Controller
{
    public function appointmentReport(Request $request)
    {
        // 1. Date-Range Filtering Logic
        $range = $request->query('range', 'daily'); // options: 'daily', 'weekly', 'monthly'

        $startDate = match ($range) {
            'weekly' => now()->startOfWeek(),
            'monthly' => now()->startOfMonth(),
            default => now()->startOfDay() // daily
        };
        $endDate = match ($range) {
            'weekly' => now()->endOfWeek(),
            'monthly' => now()->endOfMonth(),
            default => now()->endOfDay()
        };

        // 2. Build the Query
        $query = Appointment::with(['user', 'pet'])
            ->whereBetween('appointment_date', [$startDate, $endDate]);

        // 3. Status Summary logic
        $summary = [
            'total' => (clone $query)->count(),
            'completed' => (clone $query)->whereIn('status', ['completed', 'Done'])->count(),
            'missed' => (clone $query)->where('status', 'missed')->count(),
            'cancelled' => (clone $query)->where('status', 'cancelled')->count(),
        ];

        // 4. Patient List Details
        $appointments = (clone $query)->orderBy('appointment_date', 'asc')->get();

        return view('admin.reports.appointments', compact('appointments', 'summary', 'range', 'startDate', 'endDate'));
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
}
