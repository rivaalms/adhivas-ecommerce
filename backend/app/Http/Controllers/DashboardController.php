<?php

namespace App\Http\Controllers;

use App\Enums\Enum\OrderStatusEnum;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class DashboardController extends Controller
{
    /**
     * Get dashboard summary statistics (total revenue, total orders, average order value).
     */
    public function stats(Request $request)
    {
        Gate::authorize('role:admin', Auth::user());

        [$startDate, $endDate] = $this->parseDateRange($request);

        $revenue = (float) Order::where('status', OrderStatusEnum::COMPLETED)
            ->when(isset($startDate) && isset($endDate), function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('order_date', [$startDate, $endDate]);
            })
            ->sum('total_amount');
        $orders = (int) Order::where('status', OrderStatusEnum::COMPLETED)
            ->when(isset($startDate) && isset($endDate), function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('order_date', [$startDate, $endDate]);
            })
            ->count();
        $avgOrderValue = $orders > 0 ? (float) ($revenue / $orders) : 0.0;

        return $this->response([
            'total_revenue' => $revenue,
            'total_orders' => $orders,
            'avg_order_value' => $avgOrderValue,
            'meta' => [
                'start_date' => $startDate?->toDateString(),
                'end_date' => $endDate?->toDateString(),
            ],
        ], 'Dashboard stats retrieved successfully');
    }

    /**
     * Get order status breakdown (pie chart data).
     */
    public function statusBreakdown(Request $request)
    {
        Gate::authorize('role:admin', Auth::user());

        [$startDate, $endDate] = $this->parseDateRange($request);

        $breakdownData = Order::when(isset($startDate) && isset($endDate), function ($query) use ($startDate, $endDate) {
            return $query->whereBetween('order_date', [$startDate, $endDate]);
        })
            ->select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_amount) as total_revenue'))
            ->groupBy('status')
            ->get()
            ->keyBy(function ($item) {
                return $item->status instanceof OrderStatusEnum ? $item->status->value : $item->status;
            });

        $orderStatusBreakdown = [];
        foreach (OrderStatusEnum::cases() as $case) {
            $statusVal = $case->value;
            $hasData = $breakdownData->has($statusVal);
            $orderStatusBreakdown[] = [
                'status' => $statusVal,
                'count' => $hasData ? (int) $breakdownData[$statusVal]->count : 0,
                'total_revenue' => $hasData ? (float) ($breakdownData[$statusVal]->total_revenue ?? 0.0) : 0.0,
            ];
        }

        return $this->response([
            'order_status_breakdown' => $orderStatusBreakdown,
            'meta' => [
                'start_date' => $startDate?->toDateString(),
                'end_date' => $endDate?->toDateString(),
            ],
        ], 'Order status breakdown retrieved successfully');
    }

    /**
     * Get sales trend daily chart data (bar/line chart).
     */
    public function salesTrend(Request $request)
    {
        Gate::authorize('role:admin', Auth::user());

        [$startDate, $endDate] = $this->parseDateRange($request);
        if (!isset($startDate)) {
            $startDate = now()->subWeek()->startOfDay();
        }

        if (!isset($endDate)) {
            $endDate = now()->endOfDay();
        }

        $trendData = Order::where('status', OrderStatusEnum::COMPLETED)
            ->when(isset($startDate) && isset($endDate), function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('order_date', [$startDate, $endDate]);
            })
            ->select(
                DB::raw('CAST(order_date AS DATE) as date'),
                DB::raw('SUM(total_amount) as revenue'),
                DB::raw('COUNT(*) as order_count')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->keyBy(function ($item) {
                return Carbon::parse($item->date)->toDateString();
            });

        $salesTrend = [];
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            $dateStr = $currentDate->toDateString();
            $hasTrend = $trendData->has($dateStr);
            $salesTrend[] = [
                'date' => $dateStr,
                'revenue' => $hasTrend ? (float) $trendData[$dateStr]->revenue : 0.0,
                'order_count' => $hasTrend ? (int) $trendData[$dateStr]->order_count : 0,
            ];
            $currentDate = $currentDate->addDay();
        }

        return $this->response([
            'sales_trend' => $salesTrend,
            'meta' => [
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
            ],
        ], 'Sales trend retrieved successfully');
    }

    /**
     * Helper to parse start and end dates from the request.
     *
     * @return array{\Carbon\Carbon|null,\Carbon\Carbon|null}
     */
    private function parseDateRange(Request $request): array
    {
        if ($request->filled('start_date')) {
            $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
        } else {
            $startDate = null;
        }

        if ($request->filled('end_date')) {
            $endDate = Carbon::parse($request->input('end_date'))->endOfDay();
        } else {
            $endDate = null;
        }

        return [$startDate, $endDate];
    }
}
