<?php

namespace App\Services\Products\DashboardStatService;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Carbon\CarbonPeriod;

class DashboardStatRepository 
{
      public function index($request)
    {
        try {
  
          if (is_array($request) && isset($request['start_date'])) {
           
            $startDate = $request['start_date'];
            $endDate = \Carbon\Carbon::parse($startDate)->addDays(6)->toDateString(); 
          
        } else {
         
            $endDate = now(); 
            $startDate = now()->subDays(6)->startOfDay()->toDateString(); 
        }
          
           // $activeUsers = DB::table('users')->whereIn('type_id', [1, 2])->count();
            $activeUsers = DB::table('users')->where('type_id',"<", 3)->count();
             $customers = DB::table('customers')->count();
            $suppliers = DB::table('users')->where('type_id', 3)->count();
            $totalProduct = DB::table('products')->count();
            $totalProductType = DB::table('product_types')->count();

            // daily quantity sold
            $dailyProductTypeQuantitySold = DB::table('sales')
                ->whereDate('created_at', now()->toDateString())
                ->sum('quantity');
            
            // daily profit made
            $totalProductTypeDailyProfits = DB::table('sales')
                ->join('prices', 'sales.price_id', '=', 'prices.id')
                ->where('prices.status', 1)
                ->whereDate('sales.created_at', now()->toDateString())
                ->sum(DB::raw('(sales.price_sold_at - prices.cost_price) * sales.quantity'));

                

            // Retrieve sales data from the last 7 days.
              $salesData = DB::table('sales') 
              ->whereBetween('created_at', [$startDate, $endDate]) 
              ->groupBy(DB::raw('Date(created_at)')) // Group the results by day, ensuring data is aggregated day by day.
              ->select(DB::raw('Date(created_at) as day'), DB::raw('SUM(quantity) as daily_sales')) 
              ->get()
              ->keyBy('day'); // Index the resulting collection by 'day' so each entry can be accessed by its date.

              // Retrieve profit data 
            //   $profitsData = DB::table('sales') 
            //   ->join('purchases', 'sales.product_type_id', '=', 'purchases.product_type_id') 
            //   ->whereBetween('sales.created_at',[$startDate, $endDate]) 
            //   ->groupBy(DB::raw('Date(sales.created_at)')) // Group the results by the day the sale was made.
            //   ->select(DB::raw('Date(sales.created_at) as day'), DB::raw('SUM(sales.price_sold_at - purchases.price) as daily_profit')) 
            //   ->get()
            //   ->keyBy('day'); // Index the resulting collection by 'day' for easy access by date.
            $profitsData = DB::table('sales')
            ->join('prices', 'sales.product_type_id', '=', 'prices.product_type_id')
            ->where('prices.status', 1) // Only consider prices where status is 1
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->groupBy(DB::raw('Date(sales.created_at)')) // Group the results by the day the sale was made.
            ->select(
                DB::raw('Date(sales.created_at) as day'),
                DB::raw('SUM((sales.price_sold_at - prices.cost_price) * sales.quantity) as daily_profit')
            )
            ->get()
            ->keyBy('day'); // Index the resulting collection by 'day' for easy access by date.



            // Prepare for days with missing data for both sales and profits
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
                "total_product" => $totalProduct,
                "total_product_type" => $totalProductType,
                "daily_product_type_quantity_sold" => $dailyProductTypeQuantitySold,
                "total_product_type_daily_profits" => $totalProductTypeDailyProfits." NGN",
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
