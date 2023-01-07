<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends OrderFunctions
{
    public function index(Request $request)
    {
        $from = $request->get('from');
        $to = $request->get('to');
        $search = $request->get('d_name');

        $allOrders = $this->allOrders($from, $to, $search);

        return view('order_page', [
            'allOrders' => $allOrders,
        ]);
    }

    public function show($order_id)
    {
        $details = DB::select('SELECT p.sku, p.name, p.price, o.qantity FROM `order_items` as o INNER JOIN `products` as p ON o.product_id = p.id WHERE o.order_id = ?', [$order_id]);
        return $details;
    }

    public function autocomplete(Request $request)
    {
        $filtered_result = DB::table('users')
        ->select('users.first_name')
        ->join('user_category','users.id','=','user_category.user_id')
        ->where('user_category.category_id','=',1)
        ->where('users.first_name','LIKE',"%{$request->d_name}%")
        ->get();

        $data = array();
        foreach ($filtered_result as $i) {
            $data[] = $i->first_name;
        }
        $filtered_result = $data;
        return response()->json($filtered_result);

    }

    public function rank()
    {
        DB::statement('CREATE TEMPORARY TABLE table1
        SELECT DISTINCT o.order_id, SUM(p.price*o.qantity) as total FROM `order_items` as o INNER JOIN products as p on o.product_id = p.id GROUP BY o.order_id');
        DB::statement('CREATE TEMPORARY TABLE table2
        SELECT o.id, u.referred_by FROM `orders` as o INNER JOIN users AS u ON o.purchaser_id = u.id');

        DB::statement('CREATE TEMPORARY TABLE table3
        SELECT DISTINCT t2.referred_by as dId, SUM(t1.total) as sales FROM `table1` as t1 INNER JOIN table2 as t2 ON t1.order_id = t2.id GROUP BY dId ORDER BY sales DESC LIMIT 100');

        $ranked = DB::select('SELECT u.first_name, u.last_name, t3.sales, DENSE_RANK() OVER (ORDER BY t3.sales DESC) AS position FROM `table3` AS t3 INNER JOIN users AS u ON t3.dId = u.id GROUP BY u.first_name, u.last_name, t3.sales ORDER BY position ASC');

        return view('rankings', [
            'ranked' => $ranked,
        ]);

    }
}
