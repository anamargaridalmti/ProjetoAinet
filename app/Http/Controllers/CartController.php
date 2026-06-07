<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TshirtImage;
use App\Models\Color;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{

    public function show()
    {
        $cart = session()->get('cart', []);
        $prices = DB::table('prices')->first();

        $total = 0;

        // Recalcula os subtotais e totais em tempo real para garantir consistência
        foreach ($cart as $key => $item) {
            // Verifica se atinge o limiar de desconto de quantidade
            $applyDiscount = $item['qty'] >= $prices->qty_discount;

            // Determina se a imagem é de catálogo (customer_id nulo) ou própria
            $isCatalog = is_null($item['customer_id']);

            if ($isCatalog) {
                $unitPrice = $applyDiscount ? $prices->unit_price_catalog_discount : $prices->unit_price_catalog;
            } else {
                $unitPrice = $applyDiscount ? $prices->unit_price_own_discount : $prices->unit_price_own;
            }

            $cart[$key]['unit_price'] = $unitPrice;
            $cart[$key]['subtotal'] = $unitPrice * $item['qty'];

            $total += $cart[$key]['subtotal'];
        }

        // Atualiza a sessão com os valores frescos calculados
        session()->put('cart', $cart);

        return view('cart.show', compact('cart', 'total', 'prices'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'tshirt_image_id' => 'required|exists:tshirt_images,id',
            'color_code' => 'required|exists:colors,code',
            'size' => 'required|in:XS,S,M,L,XL',
            'qty' => 'required|integer|min:1',
        ]);

        $image = TshirtImage::findOrFail($request->tshirt_image_id);
        $color = Color::findOrFail($request->color_code);

        $cart = session()->get('cart', []);


        $cartKey = $image->id . '_' . $color->code . '_' . $request->size;

        if (isset($cart[$cartKey])) {
            // Se já existe, acumula a quantidade
            $cart[$cartKey]['qty'] += $request->qty;
        } else {
            // Se for novo, monta a estrutura base do item
            $cart[$cartKey] = [
                'tshirt_image_id' => $image->id,
                'name' => $image->name,
                'image_url' => $image->image_url,
                'customer_id' => $image->customer_id, // Identifica se é catálogo ou privado
                'color_code' => $color->code,
                'color_name' => $color->name,
                'size' => $request->size,
                'qty' => $request->qty,
                'unit_price' => 0, // Calculado dinamicamente no show()
                'subtotal' => 0,
            ];
        }

        session()->put('cart', $cart);

        return redirect()->route('cart.show')->with('status', 'Item adicionado ao carrinho com sucesso!');
    }

    public function update(Request $request, $key)
    {
        $cart = session()->get('cart', []);

        if (!isset($cart[$key])) {
            return redirect()->route('cart.show')->with('error', 'Item não encontrado no carrinho.');
        }

        // Se a quantidade for reduzida para 0, remove automaticamente o item
        if ($request->qty <= 0) {
            unset($cart[$key]);
            session()->put('cart', $cart);
            return redirect()->route('cart.show')->with('status', 'Item removido do carrinho.');
        }

        // Valida se houve mudança de atributos (cor ou tamanho)
        if ($request->has('size') || $request->has('color_code')) {
            $newSize = $request->input('size', $cart[$key]['size']);
            $newColorCode = $request->input('color_code', $cart[$key]['color_code']);

            $newKey = $cart[$key]['tshirt_image_id'] . '_' . $newColorCode . '_' . $newSize;

            // Se mudou para uma combinação que já existe noutra linha, funde as duas
            if ($newKey !== $key && isset($cart[$newKey])) {
                $cart[$newKey]['qty'] += $request->input('qty', $cart[$key]['qty']);
                unset($cart[$key]);
            } else {
                // Caso contrário, atualiza os dados na linha atual e troca a chave do array
                $color = Color::findOrFail($newColorCode);
                $cart[$key]['size'] = $newSize;
                $cart[$key]['color_code'] = $color->code;
                $cart[$key]['color_name'] = $color->name;
                $cart[$key]['qty'] = $request->input('qty', $cart[$key]['qty']);

                if ($newKey !== $key) {
                    $cart[$newKey] = $cart[$key];
                    unset($cart[$key]);
                }
            }
        } else {
            // Ajuste simples de quantidade
            $cart[$key]['qty'] = $request->qty;
        }

        session()->put('cart', $cart);

        return redirect()->route('cart.show')->with('status', 'Carrinho atualizado!');
    }


    public function destroy($key)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$key])) {
            unset($cart[$key]);
            session()->put('cart', $cart);
        }

        return redirect()->route('cart.show')->with('status', 'O item foi removido do carrinho.');
    }


    public function clear()
    {
        session()->forget('cart');
        return redirect()->route('cart.show')->with('status', 'O seu carrinho está vazio.');
    }
}
