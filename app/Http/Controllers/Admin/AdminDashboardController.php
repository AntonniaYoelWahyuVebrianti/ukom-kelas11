<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $totalRevenue = Order::whereNotIn('status', ['cancelled'])
            ->sum('total');

        $ordersCount = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $customersCount = User::has('orders')->count();

        $topProducts = OrderItem::query()
            ->select('product_id', DB::raw('SUM(quantity) as total_quantity'), DB::raw('SUM(total) as total_amount'))
            ->with(['product' => function ($query) {
                $query->select('id', 'name', 'image_url', 'category');
            }])
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();

        $salesByMonth = Order::query()
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(total) as total')
            ->where('created_at', '>=', Carbon::now()->subMonths(5)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(fn ($row) => [
                'month' => Carbon::createFromFormat('Y-m', $row->month)->translatedFormat('M Y'),
                'total' => (int) $row->total,
            ]);

        $inventory = Product::query()
            ->select('id', 'name', 'stock')
            ->orderBy('stock')
            ->limit(5)
            ->get();

        return response()->json([
            'cards' => [
                ['label' => 'Total Revenue', 'value' => $totalRevenue],
                ['label' => 'Total Orders', 'value' => $ordersCount],
                ['label' => 'Pending Orders', 'value' => $pendingOrders],
                ['label' => 'Active Customers', 'value' => $customersCount],
            ],
            'top_products' => $topProducts,
            'sales_by_month' => $salesByMonth,
            'low_stock' => $inventory,
        ]);
    }
}
