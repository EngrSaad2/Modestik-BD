<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Order, OrderItem, Product, User, Category, Brand};
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    protected function getDatesFromFilter($filter, $startDateStr = null, $endDateStr = null)
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        switch ($filter) {
            case 'today':
                $startDate = Carbon::now()->startOfDay();
                $endDate = Carbon::now()->endOfDay();
                break;
            case 'yesterday':
                $startDate = Carbon::now()->subDay()->startOfDay();
                $endDate = Carbon::now()->subDay()->endOfDay();
                break;
            case 'last_7_days':
                $startDate = Carbon::now()->subDays(7)->startOfDay();
                $endDate = Carbon::now()->endOfDay();
                break;
            case 'last_30_days':
                $startDate = Carbon::now()->subDays(30)->startOfDay();
                $endDate = Carbon::now()->endOfDay();
                break;
            case 'this_month':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
            case 'last_month':
                $startDate = Carbon::now()->subMonth()->startOfMonth();
                $endDate = Carbon::now()->subMonth()->endOfMonth();
                break;
            case 'this_year':
                $startDate = Carbon::now()->startOfYear();
                $endDate = Carbon::now()->endOfYear();
                break;
            case 'custom':
                if ($startDateStr && $endDateStr) {
                    try {
                        $startDate = Carbon::parse($startDateStr)->startOfDay();
                        $endDate = Carbon::parse($endDateStr)->endOfDay();
                    } catch (\Exception $e) {}
                }
                break;
        }

        return [$startDate, $endDate];
    }

    protected function applyOrderFilters($query, Request $request, $startDate, $endDate)
    {
        if ($startDate && $endDate) {
            $query->whereBetween('orders.created_at', [$startDate, $endDate]);
        }
        if ($request->filled('payment_method')) {
            $query->where('orders.payment_method', $request->payment_method);
        }
        if ($request->filled('payment_status')) {
            $query->where('orders.payment_status', $request->payment_status);
        }
        if ($request->filled('status')) {
            $query->where('orders.status', $request->status);
        }
        if ($request->filled('district')) {
            $query->where('orders.district', 'like', '%' . $request->district . '%');
        }
        if ($request->filled('division')) {
            $query->where('orders.division', 'like', '%' . $request->division . '%');
        }
        if ($request->filled('courier_name')) {
            $query->where('orders.courier_name', $request->courier_name);
        }
        if ($request->filled('source')) {
            $query->where('orders.source', $request->source);
        }
        if ($request->filled('customer_id')) {
            $query->where('orders.user_id', $request->customer_id);
        }
        if ($request->filled('category_id') || $request->filled('product_id') || $request->filled('brand_id')) {
            $query->whereHas('items.product', function ($q) use ($request) {
                if ($request->filled('product_id')) $q->where('products.id', $request->product_id);
                if ($request->filled('category_id')) $q->where('products.category_id', $request->category_id);
                if ($request->filled('brand_id')) $q->where('products.brand_id', $request->brand_id);
            });
        }
        return $query;
    }

    public function sales(Request $request)
    {
        $filter = $request->get('filter', 'this_month');
        $activeTab = $request->get('tab', 'overview');
        [$startDate, $endDate] = $this->getDatesFromFilter($filter, $request->get('start_date'), $request->get('end_date'));

        // Base Orders Queries
        $baseQuery = Order::query();
        $baseQuery = $this->applyOrderFilters($baseQuery, $request, $startDate, $endDate);

        $validOrdersQuery = (clone $baseQuery)->whereNotIn('orders.status', ['cancelled', 'returned']);
        $returnedOrdersQuery = (clone $baseQuery)->where('orders.status', 'returned');
        $cancelledOrdersQuery = (clone $baseQuery)->where('orders.status', 'cancelled');
        $paidOrdersQuery = (clone $baseQuery)->where('orders.payment_status', 'paid');
        $pendingPaymentQuery = (clone $baseQuery)->where('orders.payment_status', 'pending');

        $validOrderIds = (clone $validOrdersQuery)->pluck('orders.id');

        // 14 KPI Card Metrics
        $totalSales = (float)$validOrdersQuery->sum('total');
        $totalOrdersCount = $validOrdersQuery->count();
        $totalDiscounts = (float)$validOrdersQuery->sum('discount');
        $deliveryCharges = (float)$validOrdersQuery->sum('shipping_charge');
        $returnedCount = $returnedOrdersQuery->count();
        $refundAmount = (float)$returnedOrdersQuery->sum('total');
        $netSales = max(0, $totalSales - $totalDiscounts - $refundAmount);

        $totalProductCost = (float)OrderItem::whereIn('order_id', $validOrderIds)
            ->selectRaw('SUM(COALESCE(cost, 0) * quantity) as total_cost')
            ->value('total_cost');

        $grossProfit = max(0, $totalSales - $totalProductCost);
        $totalExpenses = $deliveryCharges * 0.15 + 2000; // estimated operational costs
        $netProfit = max(0, $grossProfit + $deliveryCharges - $totalDiscounts - $refundAmount);
        $aov = $totalOrdersCount > 0 ? $totalSales / $totalOrdersCount : 0;
        $pendingPaymentsAmount = (float)$pendingPaymentQuery->sum('total');
        $paidOrdersCount = $paidOrdersQuery->count();

        $kpis = [
            'total_sales' => $totalSales,
            'total_orders' => $totalOrdersCount,
            'net_sales' => $netSales,
            'gross_profit' => $grossProfit,
            'total_cost' => $totalProductCost,
            'total_discounts' => $totalDiscounts,
            'delivery_charges' => $deliveryCharges,
            'total_expenses' => $totalExpenses,
            'net_profit' => $netProfit,
            'aov' => $aov,
            'returned_orders' => $returnedCount,
            'refund_amount' => $refundAmount,
            'pending_payments' => $pendingPaymentsAmount,
            'paid_orders' => $paidOrdersCount,
        ];

        // 10 Interactive Charts Data
        // 1. Daily Sales Trend
        $dailySalesData = (clone $validOrdersQuery)
            ->selectRaw('DATE(created_at) as date, SUM(total) as total_sales')
            ->groupBy('date')
            ->pluck('total_sales', 'date')
            ->toArray();

        $period = CarbonPeriod::create($startDate, $endDate);
        $dailyLabels = [];
        $dailyValues = [];
        foreach ($period as $date) {
            $dStr = $date->format('Y-m-d');
            $dailyLabels[] = $date->format('d M');
            $dailyValues[] = (float)($dailySalesData[$dStr] ?? 0);
        }

        // 2. Monthly Sales Trend (Current Year)
        $monthlySalesData = Order::selectRaw('MONTH(created_at) as month_num, SUM(total) as total_sales')
            ->whereYear('created_at', Carbon::now()->year)
            ->whereNotIn('status', ['cancelled', 'returned'])
            ->groupBy('month_num')
            ->pluck('total_sales', 'month_num')
            ->toArray();

        $monthlyLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $monthlyValues = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyValues[] = (float)($monthlySalesData[$m] ?? 0);
        }

        // 3. Orders by Status
        $statusDistribution = (clone $baseQuery)
            ->selectRaw('status, COUNT(id) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // 4. Top Selling Products
        $topSellingProductsChart = OrderItem::whereIn('order_id', $validOrderIds)
            ->select('name')
            ->selectRaw('SUM(quantity) as total_qty, SUM(total) as total_rev')
            ->groupBy('name')
            ->orderByDesc('total_rev')
            ->limit(7)
            ->get();

        // 5. Top Categories
        $topCategoriesChart = OrderItem::whereIn('order_items.order_id', $validOrderIds)
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.name as category_name')
            ->selectRaw('SUM(order_items.total) as total_rev')
            ->groupBy('categories.name')
            ->orderByDesc('total_rev')
            ->limit(6)
            ->get();

        // 6. Sales by District
        $districtSalesChart = (clone $validOrdersQuery)
            ->whereNotNull('district')
            ->where('district', '!=', '')
            ->selectRaw('district, SUM(total) as total_rev')
            ->groupBy('district')
            ->orderByDesc('total_rev')
            ->limit(6)
            ->get();

        // 7. Payment Method Distribution
        $paymentMethodsChart = (clone $validOrdersQuery)
            ->selectRaw('payment_method, COUNT(id) as total_orders')
            ->groupBy('payment_method')
            ->get();

        // 8. Return Rate
        $totalAllOrdersCount = (clone $baseQuery)->count();
        $returnRate = $totalAllOrdersCount > 0 ? round(($returnedCount / $totalAllOrdersCount) * 100, 1) : 0;

        // 9. New vs Returning Customers
        $customerTypeChart = (clone $validOrdersQuery)
            ->selectRaw("CASE WHEN user_id IS NULL THEN 'Guest / New' ELSE 'Registered Customer' END as customer_type, COUNT(id) as total_count")
            ->groupBy('customer_type')
            ->get();

        // Sub-Reports Datasets

        // 1. Product Sales Section
        $productReports = OrderItem::whereIn('order_items.order_id', $validOrderIds)
            ->leftJoin('products', 'order_items.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->select('order_items.product_id', 'order_items.name', 'order_items.sku', 'categories.name as category_name', 'products.quantity as current_stock', 'products.cost as current_cost', 'products.price as selling_price')
            ->selectRaw('SUM(order_items.quantity) as units_sold')
            ->selectRaw('SUM(order_items.total) as revenue')
            ->selectRaw('SUM(COALESCE(order_items.cost, products.cost, 0) * order_items.quantity) as product_cost')
            ->groupBy('order_items.product_id', 'order_items.name', 'order_items.sku', 'categories.name', 'products.quantity', 'products.cost', 'products.price')
            ->orderByDesc('revenue')
            ->paginate(15, ['*'], 'product_page');

        // 2. Category Section
        $categoryReports = Category::withCount('products')
            ->get()
            ->map(function ($cat) use ($validOrderIds) {
                $itemStats = OrderItem::whereIn('order_items.order_id', $validOrderIds)
                    ->join('products', 'order_items.product_id', '=', 'products.id')
                    ->where('products.category_id', $cat->id)
                    ->selectRaw('SUM(order_items.quantity) as units_sold, SUM(order_items.total) as revenue, SUM(COALESCE(order_items.cost, products.cost, 0) * order_items.quantity) as cost')
                    ->first();

                $cat->units_sold = $itemStats->units_sold ?? 0;
                $cat->revenue = $itemStats->revenue ?? 0;
                $cat->cost = $itemStats->cost ?? 0;
                $cat->profit = max(0, $cat->revenue - $cat->cost);
                $cat->stock_value = $cat->products->sum(fn($p) => $p->quantity * ($p->cost ?? 0));
                return $cat;
            });

        // 3. Customer Reports
        $customerReports = Order::whereNotNull('user_id')
            ->select('user_id', 'name', 'email', 'phone')
            ->selectRaw('COUNT(id) as total_orders')
            ->selectRaw('SUM(total) as lifetime_spend')
            ->selectRaw('AVG(total) as avg_order_value')
            ->selectRaw('MAX(created_at) as last_order_date')
            ->groupBy('user_id', 'name', 'email', 'phone')
            ->orderByDesc('lifetime_spend')
            ->paginate(15, ['*'], 'customer_page');

        // 4. Inventory Reports
        $inventoryStats = [
            'total_stock' => Product::sum('quantity'),
            'low_stock_count' => Product::where('quantity', '>', 0)->where('quantity', '<=', 5)->count(),
            'out_of_stock_count' => Product::where('quantity', '<=', 0)->count(),
            'total_inventory_value' => Product::selectRaw('SUM(quantity * COALESCE(cost, 0)) as val')->value('val') ?? 0,
        ];
        $fastMovingProducts = Product::orderByDesc('views')->limit(8)->get();
        $slowMovingProducts = Product::where('quantity', '>', 0)->orderBy('views')->limit(8)->get();

        // 5. Courier Reports
        $courierReports = Order::select('courier_name')
            ->selectRaw('COUNT(id) as total_orders')
            ->selectRaw('SUM(shipping_charge) as total_charges')
            ->selectRaw("SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered_orders")
            ->selectRaw("SUM(CASE WHEN status = 'returned' THEN 1 ELSE 0 END) as returned_orders")
            ->selectRaw("SUM(CASE WHEN payment_status = 'pending' THEN total ELSE 0 END) as cod_pending")
            ->selectRaw("SUM(CASE WHEN courier_settlement_status = 'settled' THEN 1 ELSE 0 END) as settled_orders")
            ->groupBy('courier_name')
            ->get();

        // Filters dropdown options
        $categoriesList = Category::orderBy('name')->get();
        $brandsList = Brand::orderBy('name')->get();
        $productsList = Product::select('id', 'name', 'sku')->orderBy('name')->get();

        return view('admin.reports.sales', compact(
            'filter', 'activeTab', 'startDate', 'endDate', 'kpis',
            'dailyLabels', 'dailyValues', 'monthlyLabels', 'monthlyValues',
            'statusDistribution', 'topSellingProductsChart', 'topCategoriesChart',
            'districtSalesChart', 'paymentMethodsChart', 'returnRate', 'customerTypeChart',
            'productReports', 'categoryReports', 'customerReports', 'inventoryStats',
            'fastMovingProducts', 'slowMovingProducts', 'courierReports',
            'categoriesList', 'brandsList', 'productsList'
        ));
    }

    public function products(Request $request)
    {
        return redirect()->route('admin.reports.sales', array_merge($request->query(), ['tab' => 'products']));
    }

    public function customers(Request $request)
    {
        return redirect()->route('admin.reports.sales', array_merge($request->query(), ['tab' => 'customers']));
    }

    public function export($type, Request $request)
    {
        $filter = $request->get('filter', 'this_month');
        [$startDate, $endDate] = $this->getDatesFromFilter($filter, $request->get('start_date'), $request->get('end_date'));

        $filename = "report_{$type}_" . date('Y-m-d') . ".xls";

        $headers = [
            "Content-Type" => "application/vnd.ms-excel; charset=utf-8",
            "Content-Disposition" => "attachment; filename=\"{$filename}\"",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($type, $request, $startDate, $endDate) {
            echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
            echo '<head>';
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
            echo '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Report</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
            echo '<style>';
            echo 'th { background-color: #1e293b; color: #ffffff; font-weight: bold; text-align: center; vertical-align: middle; border: 0.5pt solid #475569; padding: 8px 12px; font-size: 11pt; }';
            echo 'td { border: 0.5pt solid #cbd5e1; vertical-align: middle; padding: 6px 10px; font-size: 10pt; mso-number-format: "\@"; white-space: nowrap; }';
            echo '.text { mso-number-format: "\@"; text-align: left; }';
            echo '.num { mso-number-format: "\#\,\#\#0\.00"; text-align: right; }';
            echo '.qty { mso-number-format: "\#\,\#\#0"; text-align: center; }';
            echo '.date { mso-number-format: "yyyy-mm-dd hh\:mm\:ss"; text-align: center; white-space: nowrap; }';
            echo '</style>';
            echo '</head>';
            echo '<body>';
            echo '<table>';

            if ($type === 'products') {
                echo '<thead><tr>';
                echo '<th>Product ID</th><th>Product Name</th><th>Category</th><th>Units Sold</th>';
                echo '<th>Revenue (BDT)</th><th>Product Cost (BDT)</th><th>Gross Profit (BDT)</th><th>Stock Left</th>';
                echo '</tr></thead><tbody>';

                $items = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
                    ->leftJoin('products', 'order_items.product_id', '=', 'products.id')
                    ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                    ->whereBetween('orders.created_at', [$startDate, $endDate])
                    ->whereNotIn('orders.status', ['cancelled', 'returned'])
                    ->select('order_items.product_id', 'order_items.name', 'categories.name as category_name', 'products.quantity')
                    ->selectRaw('SUM(order_items.quantity) as units_sold')
                    ->selectRaw('SUM(order_items.total) as revenue')
                    ->selectRaw('SUM(COALESCE(order_items.cost, products.cost, 0) * order_items.quantity) as total_cost')
                    ->groupBy('order_items.product_id', 'order_items.name', 'categories.name', 'products.quantity')
                    ->orderByDesc('revenue')
                    ->get();

                foreach ($items as $p) {
                    $profit = max(0, $p->revenue - $p->total_cost);
                    echo '<tr>';
                    echo "<td class='text'>{$p->product_id}</td>";
                    echo "<td>" . e($p->name) . "</td>";
                    echo "<td>" . e($p->category_name ?? '-') . "</td>";
                    echo "<td class='qty'>{$p->units_sold}</td>";
                    echo "<td class='num'>" . number_format($p->revenue, 2, '.', '') . "</td>";
                    echo "<td class='num'>" . number_format($p->total_cost, 2, '.', '') . "</td>";
                    echo "<td class='num'>" . number_format($profit, 2, '.', '') . "</td>";
                    echo "<td class='qty'>{$p->quantity}</td>";
                    echo '</tr>';
                }
                echo '</tbody>';
            } elseif ($type === 'categories') {
                echo '<thead><tr>';
                echo '<th>Category ID</th><th>Category Name</th><th>Total Products</th><th>Units Sold</th>';
                echo '<th>Revenue (BDT)</th><th>Cost (BDT)</th><th>Gross Profit (BDT)</th><th>Total Stock Value (BDT)</th>';
                echo '</tr></thead><tbody>';

                $validOrderIds = Order::whereBetween('created_at', [$startDate, $endDate])
                    ->whereNotIn('status', ['cancelled', 'returned'])
                    ->pluck('id');

                $categories = Category::withCount('products')->get();
                foreach ($categories as $cat) {
                    $itemStats = OrderItem::whereIn('order_items.order_id', $validOrderIds)
                        ->join('products', 'order_items.product_id', '=', 'products.id')
                        ->where('products.category_id', $cat->id)
                        ->selectRaw('SUM(order_items.quantity) as units_sold, SUM(order_items.total) as revenue, SUM(COALESCE(order_items.cost, products.cost, 0) * order_items.quantity) as cost')
                        ->first();

                    $unitsSold = $itemStats->units_sold ?? 0;
                    $revenue = $itemStats->revenue ?? 0;
                    $cost = $itemStats->cost ?? 0;
                    $profit = max(0, $revenue - $cost);
                    $stockVal = $cat->products->sum(fn($p) => $p->quantity * ($p->cost ?? 0));

                    echo '<tr>';
                    echo "<td class='text'>{$cat->id}</td>";
                    echo "<td>" . e($cat->name) . "</td>";
                    echo "<td class='qty'>{$cat->products_count}</td>";
                    echo "<td class='qty'>{$unitsSold}</td>";
                    echo "<td class='num'>" . number_format($revenue, 2, '.', '') . "</td>";
                    echo "<td class='num'>" . number_format($cost, 2, '.', '') . "</td>";
                    echo "<td class='num'>" . number_format($profit, 2, '.', '') . "</td>";
                    echo "<td class='num'>" . number_format($stockVal, 2, '.', '') . "</td>";
                    echo '</tr>';
                }
                echo '</tbody>';
            } elseif ($type === 'customers') {
                echo '<thead><tr>';
                echo '<th>User ID</th><th>Customer Name</th><th>Email</th><th>Phone</th>';
                echo '<th>Total Orders</th><th>Lifetime Spend (BDT)</th><th>Avg Order Value (BDT)</th><th>Last Order Date</th>';
                echo '</tr></thead><tbody>';

                $customers = Order::whereNotNull('user_id')
                    ->select('user_id', 'name', 'email', 'phone')
                    ->selectRaw('COUNT(id) as total_orders')
                    ->selectRaw('SUM(total) as lifetime_spend')
                    ->selectRaw('AVG(total) as aov')
                    ->selectRaw('MAX(created_at) as last_order_date')
                    ->groupBy('user_id', 'name', 'email', 'phone')
                    ->orderByDesc('lifetime_spend')
                    ->get();

                foreach ($customers as $c) {
                    echo '<tr>';
                    echo "<td class='text'>{$c->user_id}</td>";
                    echo "<td>" . e($c->name) . "</td>";
                    echo "<td>" . e($c->email ?? '-') . "</td>";
                    echo "<td class='text'>" . e($c->phone) . "</td>";
                    echo "<td class='qty'>{$c->total_orders}</td>";
                    echo "<td class='num'>" . number_format($c->lifetime_spend, 2, '.', '') . "</td>";
                    echo "<td class='num'>" . number_format($c->aov, 2, '.', '') . "</td>";
                    echo "<td class='date'>" . e($c->last_order_date ? Carbon::parse($c->last_order_date)->format('Y-m-d H:i:s') : '-') . "</td>";
                    echo '</tr>';
                }
                echo '</tbody>';
            } elseif ($type === 'inventory') {
                echo '<thead><tr>';
                echo '<th>Product ID</th><th>Product Name</th><th>SKU</th><th>Category</th>';
                echo '<th>Current Stock</th><th>Unit Cost (BDT)</th><th>Unit Price (BDT)</th><th>Stock Value (BDT)</th><th>Page Views</th>';
                echo '</tr></thead><tbody>';

                $products = Product::with('category')->orderBy('quantity')->get();
                foreach ($products as $p) {
                    $stockVal = $p->quantity * ($p->cost ?? 0);
                    echo '<tr>';
                    echo "<td class='text'>{$p->id}</td>";
                    echo "<td>" . e($p->name) . "</td>";
                    echo "<td class='text'>" . e($p->sku ?? '-') . "</td>";
                    echo "<td>" . e($p->category?->name ?? '-') . "</td>";
                    echo "<td class='qty'>{$p->quantity}</td>";
                    echo "<td class='num'>" . number_format($p->cost ?? 0, 2, '.', '') . "</td>";
                    echo "<td class='num'>" . number_format($p->price, 2, '.', '') . "</td>";
                    echo "<td class='num'>" . number_format($stockVal, 2, '.', '') . "</td>";
                    echo "<td class='qty'>{$p->views}</td>";
                    echo '</tr>';
                }
                echo '</tbody>';
            } elseif ($type === 'financial') {
                echo '<thead><tr>';
                echo '<th>Financial Metric</th><th>Amount (BDT)</th><th>Notes / Breakdown</th>';
                echo '</tr></thead><tbody>';

                $baseQuery = Order::query();
                $baseQuery = $this->applyOrderFilters($baseQuery, $request, $startDate, $endDate);
                $validOrdersQuery = (clone $baseQuery)->whereNotIn('orders.status', ['cancelled', 'returned']);
                $returnedOrdersQuery = (clone $baseQuery)->where('orders.status', 'returned');
                $validOrderIds = (clone $validOrdersQuery)->pluck('orders.id');

                $totalSales = (float)$validOrdersQuery->sum('total');
                $totalDiscounts = (float)$validOrdersQuery->sum('discount');
                $deliveryCharges = (float)$validOrdersQuery->sum('shipping_charge');
                $refundAmount = (float)$returnedOrdersQuery->sum('total');

                $totalProductCost = (float)OrderItem::whereIn('order_id', $validOrderIds)
                    ->selectRaw('SUM(COALESCE(cost, 0) * quantity) as total_cost')
                    ->value('total_cost');

                $grossProfit = max(0, $totalSales - $totalProductCost);
                $totalExpenses = $deliveryCharges * 0.15 + 2000;
                $netProfit = max(0, $grossProfit + $deliveryCharges - $totalDiscounts - $refundAmount);

                $financials = [
                    ['Total Gross Revenue', $totalSales, 'Sum of valid order totals in period'],
                    ['Total Product Cost (COGS)', $totalProductCost, 'Direct cost of goods sold for valid orders'],
                    ['Gross Profit', $grossProfit, 'Gross Revenue minus Product COGS'],
                    ['Total Discounts Given', $totalDiscounts, 'Promotional and coupon discounts'],
                    ['Shipping Charges Collected', $deliveryCharges, 'Delivery fee collected from customers'],
                    ['Refund / Returns Amount', $refundAmount, 'Total monetary value of returned orders'],
                    ['Estimated Operational Expenses', $totalExpenses, 'Operational, packing and logistics overheads'],
                    ['Net Profit', $netProfit, 'Final net profit calculation'],
                ];

                foreach ($financials as $f) {
                    echo '<tr>';
                    echo "<td style='font-weight:bold;'>" . e($f[0]) . "</td>";
                    echo "<td class='num'>" . number_format($f[1], 2, '.', '') . "</td>";
                    echo "<td>" . e($f[2]) . "</td>";
                    echo '</tr>';
                }
                echo '</tbody>';
            } elseif ($type === 'courier') {
                echo '<thead><tr>';
                echo '<th>Courier Name</th><th>Total Orders</th><th>Total Shipping Charges (BDT)</th>';
                echo '<th>Delivered Orders</th><th>Returned Orders</th><th>Delivery Success Rate (%)</th><th>Pending COD (BDT)</th><th>Settled Orders</th>';
                echo '</tr></thead><tbody>';

                $couriers = Order::select('courier_name')
                    ->selectRaw('COUNT(id) as total_orders')
                    ->selectRaw('SUM(shipping_charge) as total_charges')
                    ->selectRaw("SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered_orders")
                    ->selectRaw("SUM(CASE WHEN status = 'returned' THEN 1 ELSE 0 END) as returned_orders")
                    ->selectRaw("SUM(CASE WHEN payment_status = 'pending' THEN total ELSE 0 END) as cod_pending")
                    ->selectRaw("SUM(CASE WHEN courier_settlement_status = 'settled' THEN 1 ELSE 0 END) as settled_orders")
                    ->groupBy('courier_name')
                    ->get();

                foreach ($couriers as $cr) {
                    $cName = $cr->courier_name ? ucfirst($cr->courier_name) : 'Unassigned / Local';
                    $rate = $cr->total_orders > 0 ? round(($cr->delivered_orders / $cr->total_orders) * 100, 1) : 0;
                    echo '<tr>';
                    echo "<td style='font-weight:bold;'>" . e($cName) . "</td>";
                    echo "<td class='qty'>{$cr->total_orders}</td>";
                    echo "<td class='num'>" . number_format($cr->total_charges, 2, '.', '') . "</td>";
                    echo "<td class='qty'>{$cr->delivered_orders}</td>";
                    echo "<td class='qty'>{$cr->returned_orders}</td>";
                    echo "<td class='qty'>{$rate}%</td>";
                    echo "<td class='num'>" . number_format($cr->cod_pending, 2, '.', '') . "</td>";
                    echo "<td class='qty'>{$cr->settled_orders}</td>";
                    echo '</tr>';
                }
                echo '</tbody>';
            } else {
                // Detailed Sales / Orders Report (Removed Variant/Info, SKU, Line Total, Order Subtotal, Order Discount, Shipping Charge per user request)
                echo '<thead><tr>';
                echo '<th>Order #</th><th>Date</th><th>Customer Name</th><th>Phone</th><th>District</th><th>Thana / Area</th><th>Address</th>';
                echo '<th>Product Name</th><th>Qty</th><th>Unit Price (BDT)</th><th>Unit Cost (BDT)</th><th>Grand Total (BDT)</th>';
                echo '<th>Payment Method</th><th>Payment Status</th><th>Order Status</th><th>Courier</th>';
                echo '</tr></thead><tbody>';

                $ordersQuery = Order::with(['items.product', 'items.variant']);
                $ordersQuery = $this->applyOrderFilters($ordersQuery, $request, $startDate, $endDate);
                $orders = $ordersQuery->latest()->get();

                foreach ($orders as $o) {
                    $orderDate = $o->created_at->format('Y-m-d H:i:s');
                    if ($o->items->count() > 0) {
                        foreach ($o->items as $item) {
                            echo '<tr>';
                            echo "<td class='text'>" . e($o->order_number) . "</td>";
                            echo "<td class='date'>" . e($orderDate) . "</td>";
                            echo "<td>" . e($o->name) . "</td>";
                            echo "<td class='text'>" . e($o->phone) . "</td>";
                            echo "<td>" . e($o->district) . "</td>";
                            echo "<td>" . e($o->division) . "</td>";
                            echo "<td>" . e($o->address) . "</td>";
                            echo "<td>" . e($item->name ?? $item->product?->name ?? '-') . "</td>";
                            echo "<td class='qty'>{$item->quantity}</td>";
                            echo "<td class='num'>" . number_format($item->price, 2, '.', '') . "</td>";
                            echo "<td class='num'>" . number_format($item->cost ?? 0, 2, '.', '') . "</td>";
                            echo "<td class='num'>" . number_format($o->total, 2, '.', '') . "</td>";
                            echo "<td>" . e(strtoupper($o->payment_method)) . "</td>";
                            echo "<td>" . e(ucfirst($o->payment_status)) . "</td>";
                            echo "<td>" . e(ucfirst($o->status)) . "</td>";
                            echo "<td>" . e($o->courier_name ?? '-') . "</td>";
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr>';
                        echo "<td class='text'>" . e($o->order_number) . "</td>";
                        echo "<td class='date'>" . e($orderDate) . "</td>";
                        echo "<td>" . e($o->name) . "</td>";
                        echo "<td class='text'>" . e($o->phone) . "</td>";
                        echo "<td>" . e($o->district) . "</td>";
                        echo "<td>" . e($o->division) . "</td>";
                        echo "<td>" . e($o->address) . "</td>";
                        echo "<td>-</td><td class='qty'>0</td><td class='num'>0.00</td><td class='num'>0.00</td>";
                        echo "<td class='num'>" . number_format($o->total, 2, '.', '') . "</td>";
                        echo "<td>" . e(strtoupper($o->payment_method)) . "</td>";
                        echo "<td>" . e(ucfirst($o->payment_status)) . "</td>";
                        echo "<td>" . e(ucfirst($o->status)) . "</td>";
                        echo "<td>" . e($o->courier_name ?? '-') . "</td>";
                        echo '</tr>';
                    }
                }
                echo '</tbody>';
            }

            echo '</table></body></html>';
        };

        return response()->stream($callback, 200, $headers);
    }
}
