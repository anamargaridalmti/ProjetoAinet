<?php

namespace App\Http\Controllers;

use App\Models\TshirtImage;
use App\Models\Category;
use Illuminate\Http\Request;

class TshirtImageController extends Controller
{
    /**
     * Exibe a montra pública do catálogo com filtros e pesquisa (G2)
     */
    public function index(Request $request)
    {
        // Puxamos apenas as imagens oficiais da loja (customer_id é null)
        $query = TshirtImage::whereNull('customer_id');

        // Filtro por Nome ou Descrição
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filtro por Categoria
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Corrigido: Agora guarda em $tshirts para dar match com a tua vista
        $tshirts = $query->latest()->paginate(12)->withQueryString();

        // Puxamos todas as categorias para preencher o select do filtro
        $categories = Category::all();

        return view('catalog.index', compact('tshirts', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(TshirtImage $tshirtImage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TshirtImage $tshirtImage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TshirtImage $tshirtImage)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TshirtImage $tshirtImage)
    {
        //
    }
}
