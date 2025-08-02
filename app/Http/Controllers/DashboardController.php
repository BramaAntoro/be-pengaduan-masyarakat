<?php

namespace App\Http\Controllers;

use App\Http\Resources\DashboardResource;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Exception;

class DashboardController extends Controller
{
    public function getStatistics()
    {
        try {
            $totalTickets = Ticket::count();

            $activeTickets = Ticket::where('status', '!=', 'resolved')->count();

            $resolvedTickets = Ticket::where('status', 'resolved')->count();

            $avgResolutionTime = Ticket::where('status', 'resolved')
                ->whereNotNull('completed_at')
                ->select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, completed_at)) as avg_time'))
                ->value('avg_time') ?? 0;

            $statusDistribution = [
                'open' => Ticket::where('status', 'open')->count(),
                'on_progress' => Ticket::where('status', 'on_progress')->count(),
                'resolved' => Ticket::where('status', 'resolved')->count(),
                'rejected' => Ticket::where('status', 'rejected')->count(),
            ];

            $dashboardData = [
                'total_tickets' => $totalTickets,
                'active_tickets' => $activeTickets,
                'resolved_tickets' => $resolvedTickets,
                'avg_resolution_time' => round($avgResolutionTime, 1),
                'status_distribution' => $statusDistribution,
            ];

            return response()->json([
                'message' => 'Dashboard statistics retrieved successfully.',
                'data' => new DashboardResource($dashboardData),
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve dashboard statistics.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
