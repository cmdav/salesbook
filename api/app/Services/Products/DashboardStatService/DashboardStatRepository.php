<?php

namespace App\Services\Products\DashboardStatService;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\CarbonPeriod;

class DashboardStatRepository
{
    public function index($request)
    {
        try {
            $user = Auth::user(); // Get the authenticated user

            $branchId = $user->branch_id; // Assuming branch_id is a property of the user

            if (is_array($request) && isset($request['start_date'])) {
                $startDate = $request['start_date'];
                $endDate = \Carbon\Carbon::parse($startDate)->addDays(6)->toDateString();
            } else {
                $endDate = now();
                $startDate = now()->subDays(6)->startOfDay()->toDateString();
            }

            // Apply branch_id filter to relevant queries
            $activeUsers = DB::table('users')
                ->where('type_id', "<", 3)
                ->where('branch_id', $branchId)
                ->count();

            $customers = DB::table('customers')
                ->where('branch_id', $branchId)
                ->count();

            $suppliers = DB::table('users')
                ->where('type_id', 3)
                //->where('branch_id', $branchId)
                ->count();

            //$totalProduct = DB::table('products')
            //->where('branch_id', $branchId)
            //  ->count();

            $totalProductType = DB::table('product_types')
                //->where('branch_id', $branchId)
                ->count();

            // // Daily quantity sold
            $dailyProductTypeQuantitySold = DB::table('sales')
                ->where('branch_id', $branchId)
                ->whereDate('created_at', now()->toDateString())
                ->sum('quantity');

            // Daily profit made
            $totalProductTypeDailyProfits = DB::table('sales')
                ->join('prices', 'sales.price_id', '=', 'prices.id')
                ->where('sales.branch_id', $branchId)
                ->whereDate('sales.created_at', now()->toDateString())
                ->sum(DB::raw('
                    (sales.price_sold_at - CASE 
                        WHEN prices.is_new = 1 THEN prices.new_cost_price 
                        ELSE prices.cost_price 
                    END) * sales.quantity
                '));

            // // Retrieve sales data from the last 7 days
            $salesData = DB::table('sales')
                ->where('branch_id', $branchId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy(DB::raw('Date(created_at)'))
                ->select(DB::raw('Date(created_at) as day'), DB::raw('SUM(quantity) as daily_sales'))
                ->get()
                ->keyBy('day');

            // // Retrieve profit data
            $profitsData = DB::table('sales')
                ->join('prices', 'sales.product_type_id', '=', 'prices.product_type_id')
                ->where('sales.branch_id', $branchId)
                ->whereBetween('sales.created_at', [$startDate, $endDate])
                ->groupBy(DB::raw('Date(sales.created_at)'))
                ->select(
                    DB::raw('Date(sales.created_at) as day'),
                    DB::raw('SUM((sales.price_sold_at - CASE WHEN prices.is_new = 1 
                    THEN prices.new_cost_price ELSE prices.cost_price END) * sales.quantity) as daily_profit')
                )
                ->get()
                ->keyBy('day');

            // // Prepare for days with missing data for both sales and profits
            $period = CarbonPeriod::create($startDate, $endDate);
            $weeklyProductTypeSalesMadePerDay = [];
            $weeklyProductTypeProfitMadePerDay = [];
            foreach ($period as $date) {
                $formattedDate = $date->format('Y-m-d');
                $dayOfWeek = $date->format('l'); // Get the full name of the day of the week

                $weeklyProductTypeSalesMadePerDay[] = [
                    'day' => $formattedDate . ' (' . $dayOfWeek . ')',
                    'daily_sales' => $salesData->has($formattedDate) ? $salesData->get($formattedDate)->daily_sales : 0
                ];

                $weeklyProductTypeProfitMadePerDay[] = [
                    'day' => $formattedDate . ' (' . $dayOfWeek . ')',
                    'daily_profit' => $profitsData->has($formattedDate) ? $profitsData->get($formattedDate)->daily_profit : 0
                ];
            }

            return [
                "active_users" => $activeUsers,
                 "customers" => $customers,
                 "suppliers" => $suppliers,
                "total_product" => "",
                "total_product_type" => $totalProductType,
                "daily_product_type_quantity_sold" => $dailyProductTypeQuantitySold,
                "total_product_type_daily_profits" => $totalProductTypeDailyProfits . " NGN",
                "weekly_product_type_quantity_sales" => $weeklyProductTypeSalesMadePerDay,
                "weekly_product_type_profit_made_per_day" => $weeklyProductTypeProfitMadePerDay,
            ];
        } catch (QueryException $e) {
            Log::error('Error fetching dashboard stats: ' . $e->getMessage());
            return [
                "error" => "Unable to fetch dashboard statistics at this time."
            ];
        }
    }
}
