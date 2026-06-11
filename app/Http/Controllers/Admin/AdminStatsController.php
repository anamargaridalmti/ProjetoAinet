<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminStatsController extends Controller
{
    public function index(): View
    {
        if (auth()->user()->user_type !== 'A') {
            abort(403, 'Acesso restrito ao Administrador.');
        }

        // 1. Volume de Negócio Global (Apenas encomendas fechadas)
        $totalFaturado = Order::where('status', 'closed')->sum('total_price');
        
        // 2. Quantidade Total de T-shirts Despachadas
        $totalTshirtsVendidas = OrderItem::whereHas('order', function ($q) {
            $q->where('status', 'closed');
        })->sum('qty');

        // 3. Extração seletiva de vendas por Categoria (Requisito de Eficiência)
        $vendasPorCategoria = OrderItem::whereHas('order', function ($q) {
                $q->where('status', 'closed');
            })
            ->join('tshirt_images', 'order_items.tshirt_image_id', '=', 'tshirt_images.id')
            ->join('categories', 'tshirt_images.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('SUM(order_items.qty) as total_qty'), DB::raw('SUM(order_items.sub_total) as total_revenue'))
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('total_revenue', 'desc')
            ->get();

        // 4. Top 5 Clientes (Maior valor acumulado)
        $topClientes = Order::where('status', 'closed')
            ->join('users', 'orders.customer_id', '=', 'users.id')
            ->select('users.name', 'users.email', DB::raw('SUM(orders.total_price) as gasto_total'))
            ->groupBy('orders.customer_id', 'users.name', 'users.email')
            ->orderBy('gasto_total', 'desc')
            ->take(5)
            ->get();

        return view('admin.statistics', compact(
            'totalFaturado', 
            'totalTshirtsVendidas', 
            'vendasPorCategoria', 
            'topClientes'
        ));
    }
}