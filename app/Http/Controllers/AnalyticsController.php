<?php

namespace App\Http\Controllers;

use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    protected $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Display main analytics dashboard
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;
        
        $days = $request->get('days', 30);
        $analytics = $this->analyticsService->getDashboardAnalytics($company, $days);

        return view('analytics.dashboard', compact('analytics', 'company', 'days'));
    }

    /**
     * Get analytics data for charts (AJAX)
     */
    public function getChartData(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;
        
        $type = $request->get('type', 'appointments');
        $days = $request->get('days', 30);
        
        $analytics = $this->analyticsService->getDashboardAnalytics($company, $days);
        
        switch ($type) {
            case 'appointments':
                return response()->json([
                    'success' => true,
                    'data' => $this->formatAppointmentChartData($analytics['appointments']),
                ]);
                
            case 'revenue':
                return response()->json([
                    'success' => true,
                    'data' => $this->formatRevenueChartData($analytics['revenue']),
                ]);
                
            case 'staff':
                return response()->json([
                    'success' => true,
                    'data' => $this->formatStaffChartData($analytics['staff']),
                ]);
                
            case 'locations':
                return response()->json([
                    'success' => true,
                    'data' => $this->formatLocationChartData($analytics['locations']),
                ]);
                
            default:
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid chart type',
                ], 400);
        }
    }

    /**
     * Generate analytics report
     */
    public function generateReport(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role !== 'admin') {
            abort(403, 'Only administrators can generate reports.');
        }

        $company = $user->company;
        $startDate = $request->get('start_date', Carbon::today()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::today()->format('Y-m-d'));
        
        try {
            // Generate fresh analytics for the date range
            $currentDate = Carbon::parse($startDate);
            $endDateCarbon = Carbon::parse($endDate);
            
            while ($currentDate->lte($endDateCarbon)) {
                $this->analyticsService->generateCompanyAnalytics($company, $currentDate);
                $currentDate->addDay();
            }
            
            $analytics = $this->analyticsService->getDashboardAnalytics($company, $endDateCarbon->diffInDays($currentDate));
            
            return view('analytics.report', compact('analytics', 'company', 'startDate', 'endDate'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to generate report: ' . $e->getMessage()]);
        }
    }

    /**
     * Export analytics data
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role !== 'admin') {
            abort(403, 'Only administrators can export analytics.');
        }

        $company = $user->company;
        $format = $request->get('format', 'csv');
        $days = $request->get('days', 30);
        
        $analytics = $this->analyticsService->getDashboardAnalytics($company, $days);
        
        switch ($format) {
            case 'csv':
                return $this->exportToCsv($analytics, $company);
            case 'json':
                return $this->exportToJson($analytics, $company);
            default:
                return redirect()->back()
                    ->withErrors(['error' => 'Invalid export format.']);
        }
    }

    /**
     * Show specific metric details
     */
    public function showMetric(Request $request, $metricType, $metricName)
    {
        $user = Auth::user();
        $company = $user->company;
        
        $days = $request->get('days', 30);
        $endDate = Carbon::today();
        $startDate = $endDate->copy()->subDays($days - 1);
        
        $analytics = \App\Models\Analytics::where('company_id', $company->id)
            ->where('metric_type', $metricType)
            ->where('metric_name', $metricName)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get();
        
        return view('analytics.metric-detail', compact('analytics', 'metricType', 'metricName', 'company', 'days'));
    }

    /**
     * Format appointment data for charts
     */
    private function formatAppointmentChartData($appointmentData)
    {
        return [
            'labels' => $appointmentData['trend']->keys()->toArray(),
            'datasets' => [
                [
                    'label' => 'Total Appointments',
                    'data' => $appointmentData['trend']->values()->toArray(),
                    'borderColor' => 'rgb(75, 192, 192)',
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                ],
            ],
            'summary' => [
                'total' => $appointmentData['total'],
                'completed' => $appointmentData['completed'],
                'cancelled' => $appointmentData['cancelled'],
                'completion_rate' => round($appointmentData['completion_rate'], 1),
                'cancellation_rate' => round($appointmentData['cancellation_rate'], 1),
            ],
        ];
    }

    /**
     * Format revenue data for charts
     */
    private function formatRevenueChartData($revenueData)
    {
        return [
            'labels' => $revenueData['trend']->keys()->toArray(),
            'datasets' => [
                [
                    'label' => 'Daily Revenue',
                    'data' => $revenueData['trend']->values()->toArray(),
                    'borderColor' => 'rgb(255, 99, 132)',
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                ],
            ],
            'summary' => [
                'total' => $revenueData['total'],
                'average_per_appointment' => round($revenueData['average_per_appointment'], 2),
            ],
        ];
    }

    /**
     * Format staff data for charts
     */
    private function formatStaffChartData($staffData)
    {
        $staffNames = [];
        $appointmentData = [];
        $revenueData = [];
        
        foreach ($staffData as $staff) {
            $staffNames[] = $staff['name'];
            $appointmentData[] = $staff['appointments'];
            $revenueData[] = $staff['revenue'];
        }
        
        return [
            'labels' => $staffNames,
            'datasets' => [
                [
                    'label' => 'Appointments Handled',
                    'data' => $appointmentData,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 1,
                ],
            ],
            'revenueDataset' => [
                'label' => 'Revenue Generated',
                'data' => $revenueData,
                'backgroundColor' => 'rgba(255, 206, 86, 0.2)',
                'borderColor' => 'rgba(255, 206, 86, 1)',
                'borderWidth' => 1,
            ],
        ];
    }

    /**
     * Format location data for charts
     */
    private function formatLocationChartData($locationData)
    {
        $locationNames = [];
        $appointmentData = [];
        $revenueData = [];
        
        foreach ($locationData as $location) {
            $locationNames[] = $location['name'];
            $appointmentData[] = $location['appointments'];
            $revenueData[] = $location['revenue'];
        }
        
        return [
            'labels' => $locationNames,
            'datasets' => [
                [
                    'label' => 'Appointments',
                    'data' => $appointmentData,
                    'backgroundColor' => 'rgba(153, 102, 255, 0.2)',
                    'borderColor' => 'rgba(153, 102, 255, 1)',
                    'borderWidth' => 1,
                ],
            ],
            'revenueDataset' => [
                'label' => 'Revenue',
                'data' => $revenueData,
                'backgroundColor' => 'rgba(255, 159, 64, 0.2)',
                'borderColor' => 'rgba(255, 159, 64, 1)',
                'borderWidth' => 1,
            ],
        ];
    }

    /**
     * Export analytics to CSV
     */
    private function exportToCsv($analytics, $company)
    {
        $filename = 'analytics-' . $company->name . '-' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        
        $callback = function() use ($analytics) {
            $file = fopen('php://output', 'w');
            
            // Write headers
            fputcsv($file, ['Metric Type', 'Metric Name', 'Value', 'Date']);
            
            // Write appointment data
            foreach ($analytics['appointments']['trend'] as $date => $value) {
                fputcsv($file, ['Appointments', 'Total', $value, $date]);
            }
            
            // Write revenue data
            foreach ($analytics['revenue']['trend'] as $date => $value) {
                fputcsv($file, ['Revenue', 'Daily Revenue', $value, $date]);
            }
            
            // Write staff data
            foreach ($analytics['staff'] as $staff) {
                fputcsv($file, ['Staff', 'Appointments', $staff['appointments'], 'N/A']);
                fputcsv($file, ['Staff', 'Revenue', $staff['revenue'], 'N/A']);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export analytics to JSON
     */
    private function exportToJson($analytics, $company)
    {
        $filename = 'analytics-' . $company->name . '-' . now()->format('Y-m-d') . '.json';
        
        $exportData = [
            'company' => $company->name,
            'export_date' => now()->toISOString(),
            'analytics' => $analytics,
        ];
        
        return response()->json($exportData)
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }
}
