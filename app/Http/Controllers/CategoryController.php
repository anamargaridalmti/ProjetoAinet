<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\CategoryFormRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CategoryController extends Controller implements HasMiddleware
{
    /**
     * Define os middlewares que protegem este controlador de forma nativa no Laravel moderno
     */
    public static function middleware(): array
    {
        return [
            // Garante que apenas utilizadores administradores ('A') acedem a qualquer método
            new Middleware(function ($request, $next) {
                if (!auth()->check() || auth()->user()->user_type !== 'A') {
                    abort(403, 'Acesso restrito aos administradores da plataforma.');
                }
                return $next($request);
            }),
        ];
    }

    /**
     * Listar todas as categorias (Passo 2 - ordenar por name)
     */
    public function index()
    {
        // Vai buscar as categorias ordenadas por nome com paginação
        $categories = Category::orderBy('name')->paginate(20);

        // Aponta para a pasta admin que criaste
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Mostrar o formulário de criação de categoria
     */
    public function create()
    {
        $category = new Category(); // Objeto vazio para os fields
        // Ajustado: Aponta para a subpasta admin
        return view('admin.categories.create', compact('category'));
    }

    /**
     * Guardar uma nova categoria (Passo 3 - guardar imagem)
     */
    public function store(CategoryFormRequest $request)
    {
        $formData = $request->validated();

        // Cria uma instância temporária para aceder à Trait de ficheiros
        $category = new Category();

        // Se o utilizador enviou um ficheiro de imagem
        if ($request->hasFile('image_file')) {
            $fileName = $category->saveCategoryImageFile($request->file('image_file'));
            $formData['image_url'] = $fileName;
        }

        Category::create($formData);

        return redirect()
            ->route('categories.index')
            ->with('success', 'Categoria criada com sucesso!');
    }

    /**
     * Mostrar os detalhes de uma categoria específica (Opcional)
     */
    public function show(Category $category)
    {
        return view('admin.categories.show', compact('category'));
    }

    /**
     * Mostrar o formulário de edição de uma categoria
     */
    public function edit(Category $category)
    {
        // Ajustado: Aponta para a subpasta admin
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Atualizar a categoria (Passo 3 - atualizar imagem)
     */
    public function update(CategoryFormRequest $request, Category $category)
    {
        $formData = $request->validated();

        // Se foi feito o upload de uma nova imagem
        if ($request->hasFile('image_file')) {
            // Guarda a nova imagem e apaga automaticamente a antiga se existir
            $fileName = $category->saveCategoryImageFile($request->file('image_file'), $category->image_url);
            $formData['image_url'] = $fileName;
        }

        $category->update($formData);

        return redirect()
            ->route('categories.index')
            ->with('success', 'Categoria actualizada com sucesso!');
    }

    /**
     * Eliminar a categoria (Passo 2 - softdeletes)
     */
    public function destroy(Category $category)
    {
        // Como o modelo usa SoftDeletes, isto apenas preenche o campo deleted_at
        $category->delete();

        return redirect()
            ->route('categories.index')
            ->with('success', "A categoria '{$category->name}' foi eliminada com sucesso!");
    }

    /**
     * Método adicional para apagar a imagem individualmente (Passo 4)
     */
    public function destroyImage(Category $category)
    {
        if ($category->image_url) {
            // Apaga o ficheiro físico do disco público usando a Trait
            $category->deleteCategoryImageFile($category->image_url);

            // Coloca a coluna a null na base de dados
            $category->update(['image_url' => null]);
        }

        return back()->with('success', 'Imagem da categoria removida com sucesso!');
    }
}
