<?php

namespace App\Http\Controllers;

use App\Models\TshirtImage;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class TshirtImageController extends Controller implements HasMiddleware
{
    /**
     * Aplica segurança estrita: métodos administrativos exigem perfil 'A'
     */
    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                // Apenas tranca os métodos que começam com admin, ou create, store, edit, update, destroy
                $adminMethods = ['adminIndex', 'create', 'store', 'edit', 'update', 'destroy'];
                if (in_array($request->route()->getActionMethod(), $adminMethods)) {
                    if (!auth()->check() || auth()->user()->user_type !== 'A') {
                        abort(403, 'Acesso restrito aos administradores.');
                    }
                }
                return $next($request);
            }),
        ];
    }

    /**
     * Catálogo Público (Já existente no teu projeto - mantido intacto)
     */
    public function index(Request $request): View
    {
        $query = TshirtImage::query();

        // Filtro de pesquisa por Nome ou Descrição
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Filtro por Categoria
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Paginação do Catálogo Público (12 por página)
        $tshirtImages = $query->latest()->paginate(12);

        $tshirts = $tshirtImages;

        $categories = Category::orderBy('name')->get();

        return view('catalog.index', compact('tshirtImages', 'tshirts', 'categories'));
    }

    /**
     * Painel Administrativo do Catálogo (Tabela de Gestão)
     */
    public function adminIndex(): View
    {
        // Carrega com a relação de categoria para evitar queries duplicadas (Eager Loading)
        $tshirtImages = TshirtImage::with('category')->latest()->paginate(20);
        return view('admin.tshirt-images.index', compact('tshirtImages'));
    }

    /**
     * Formulário de Criação de nova Estampa
     */
    public function create(): View
    {
        $tshirtImage = new TshirtImage();
        $categories = Category::orderBy('name')->get();
        return view('admin.tshirt-images.create', compact('tshirtImage', 'categories'));
    }

    /**
     * Gravar nova Estampa no Catálogo (G2 - Upload Obrigatório)
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'image_file' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // Obrigatório
        ], [
            'name.required' => 'O nome da t-shirt/estampa é obrigatório.',
            'image_file.required' => 'É obrigatório fazer o upload do ficheiro de imagem da estampa.',
        ]);

        $tshirtImage = new TshirtImage();
        $tshirtImage->name = $request->name;
        $tshirtImage->description = $request->description;
        $tshirtImage->category_id = $request->category_id;
        $tshirtImage->customer_id = null; // null significa que é uma estampa oficial da loja

        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            // Gera um nome único para o ficheiro para não haver sobreposições
            $filename = uniqid('img_') . '.' . $file->getClientOriginalExtension();

            // Guarda no disco público dentro da pasta tshirt_images
            $file->storeAs('tshirt_images', $filename, 'public');
            $tshirtImage->image_url = $filename;
        }

        $tshirtImage->save();

        return redirect()->route('admin.tshirt-images.index')
            ->with('alert-type', 'success')
            ->with('alert-msg', "A estampa <strong>'{$tshirtImage->name}'</strong> foi adicionada ao catálogo com sucesso!");
    }

    /**
     * Formulário de Edição de Estampa
     */
    public function edit(TshirtImage $tshirtImage): View
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.tshirt-images.edit', compact('tshirtImage', 'categories'));
    }

    /**
     * Atualizar os dados da Estampa (G2 - Upload Opcional)
     */
    public function update(Request $request, TshirtImage $tshirtImage): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $tshirtImage->name = $request->name;
        $tshirtImage->description = $request->description;
        $tshirtImage->category_id = $request->category_id;

        if ($request->hasFile('image_file')) {
            // Elimina o ficheiro físico antigo se ele existir
            if ($tshirtImage->image_url) {
                Storage::disk('public')->delete('tshirt_images/' . $tshirtImage->image_url);
            }

            // Upload do novo
            $file = $request->file('image_file');
            $filename = uniqid('img_') . '.' . $file->getClientOriginalExtension();
            $file->storeAs('tshirt_images', $filename, 'public');
            $tshirtImage->image_url = $filename;
        }

        $tshirtImage->save();

        return redirect()->route('admin.tshirt-images.index')
            ->with('alert-type', 'success')
            ->with('alert-msg', "A estampa <strong>'{$tshirtImage->name}'</strong> foi atualizada com sucesso!");
    }

    /**
     * Eliminar a Estampa do catálogo
     */
    public function destroy(TshirtImage $tshirtImage): RedirectResponse
    {
        // Remove o ficheiro físico do servidor para não deixar lixo
        if ($tshirtImage->image_url) {
            Storage::disk('public')->delete('tshirt_images/' . $tshirtImage->image_url);
        }

        $tshirtImage->delete();

        return redirect()->route('admin.tshirt-images.index')
            ->with('alert-type', 'success')
            ->with('alert-msg', "A estampa foi removida do catálogo.");
    }
}
