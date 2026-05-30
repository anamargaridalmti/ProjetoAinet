<?php

namespace App\Http\Controllers;

use App\Models\Price;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PriceController extends Controller implements HasMiddleware
{
    /**
     * Tranca o acesso exclusivo aos Administradores
     */
    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                if (!auth()->check() || auth()->user()->user_type !== 'A') {
                    abort(403, 'Acesso restrito aos administradores.');
                }
                return $next($request);
            }),
        ];
    }

    /**
     * Mostrar o formulário com os preços atuais
     */
    public function edit(): View
    {
        // Vais buscar o único registo de configuração de preços
        $price = Price::first() ?? new Price();
        return view('admin.prices.edit', compact('price'));
    }

    /**
     * Atualizar a tabela global de preços
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'unit_price_catalog' => 'required|numeric|min:0',
            'unit_price_own' => 'required|numeric|min:0',
            'unit_price_catalog_discount' => 'required|numeric|min:0',
            'unit_price_own_discount' => 'required|numeric|min:0',
            'qty_discount' => 'required|integer|min:1',
        ], [
            'qty_discount.min' => 'A quantidade para desconto deve ser pelo menos 1 unidade.',
        ]);

        $price = Price::first();

        // Se por acaso a tabela estiver vazia, cria o registo inicial
        if (!$price) {
            $price = new Price();
        }

        $price->unit_price_catalog = $request->unit_price_catalog;
        $price->unit_price_own = $request->unit_price_own;
        $price->unit_price_catalog_discount = $request->unit_price_catalog_discount;
        $price->unit_price_own_discount = $request->unit_price_own_discount;
        $price->qty_discount = $request->qty_discount;
        $price->save();

        return redirect()->route('admin.prices.edit')
            ->with('alert-type', 'success')
            ->with('alert-msg', 'Tabela global de preços atualizada com sucesso!');
    }
}
