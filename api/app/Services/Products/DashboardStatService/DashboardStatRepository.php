<?php

namespace App\Services\Products\DashboardStatService;


use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class DashboardStatRepository 
{
    public function index()
    {
       //select all from users table
       // select from  sales
       // select from purchase table
        $activeUser="";
      return [
        
        "active_users" => $activeUsers,
        //"daily_total_products"=> 1,
        "daily_quantity_sold"=> 1,
        "total_daily_profits"=> 1,
        "weekly_quantity_sales"=> 1,
        "weekly_profit_made_per_day"=> 1,
        
      ];
       
    }
    
    
}
