<?php

namespace App\Http\Controllers\Utils;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class CustomOrderFunc extends Controller
{
    public function allOrders($from, $to, $search)
    {
        if ($from != null && $to != null && $search != null) {

          $_user = DB::table('users')
          ->select('users.id')
          ->join('user_category', 'users.id', '=', 'user_category.user_id')
          ->where('user_category.category_id', '=', 1)
          ->where('users.first_name', 'LIKE', "%{$search}%")
          ->get();

          $array = $_user->pluck('id')->toArray();

          $allOrders = DB::table('orders')
            ->select('orders.id', 'orders.invoice_number', 'orders.purchaser_id', 'orders.order_date',
              'users.first_name','users.last_name', 'users.referred_by')
            ->leftJoin('users', 'orders.purchaser_id', '=', 'users.id')
            ->whereIn('referred_by', $array)
            ->where(function($query) use ($from, $to) {
                $query->whereBetween('orders.order_date', [$from, $to]);
            })
            ->paginate(10);

           return $allOrders;
            
        }
        elseif ($from != null && $to != null) {
            $allOrders = DB::table('orders')
            ->select('orders.id', 'orders.invoice_number', 'orders.purchaser_id', 'orders.order_date', 'users.first_name', 'users.last_name', 'users.referred_by')
            ->join('users', 'orders.purchaser_id', '=', 'users.id')
            ->whereBetween('orders.order_date', [$from, $to])
            ->simplePaginate(10);
            return $allOrders;
        }
        elseif ($search != null) {

            $_user = DB::table('users')
          ->select('users.id')
          ->join('user_category','users.id','=','user_category.user_id')
          ->where('user_category.category_id','=',1)
          ->where('users.first_name','LIKE',"%{$search}%")
          ->get();
           
            $array = $_user->pluck('id')->toArray();

            $allOrders = DB::table('orders')
            ->select('orders.id', 'orders.invoice_number', 'orders.purchaser_id', 'orders.order_date', 'users.first_name', 'users.last_name', 'users.referred_by')
            ->join('users', 'orders.purchaser_id', '=', 'users.id')
            ->whereIn('referred_by', $array)
            ->simplePaginate(10);

            return $allOrders;
            
        } 
        else {
            $allOrders = DB::table('orders')
            ->select('orders.id', 'orders.invoice_number', 'orders.purchaser_id', 'orders.order_date', 'users.first_name', 'users.last_name', 'users.referred_by')
            ->join('users', 'orders.purchaser_id', '=', 'users.id')
            ->simplePaginate(10);
            return $allOrders;
        }
    }

}
