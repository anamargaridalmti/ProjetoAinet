<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Color;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\ColorFormRequest;

class ColorController extends Controller
{
    /**
     * Listar todas as cores (Passo 2 - ordenar apenas por name)
     */
    public function index(): View
    {
        $colors = Color::orderBy('name')->paginate(20);

        return view('colors.index', compact('colors'));
    }

    /**
     * Mostrar os detalhes de uma cor específica
     */
    public function show(Color $color): View
    {
        return view('colors.show', compact('color'));
    }

    /**
     * Mostrar o formulário de criação de cor
     */
    public function create(): View
    {
        $color = new Color();
        return view('colors.create', compact('color'));
    }

    /**
     * Guardar uma nova cor
     */
    public function store(ColorFormRequest $request): RedirectResponse
    {
        $newColor = Color::create($request->validated());

        $url = route('colors.show', ['color' => $newColor]);
        $htmlMessage = "A cor <a href='$url'><strong>{$newColor->code}</strong> - '{$newColor->name}'</a> foi criada com sucesso!";

        return redirect()->route('colors.index')
            ->with('alert-type', 'success')
            ->with('alert-msg', $htmlMessage);
    }

    /**
     * Mostrar o formulário de edição de cor
     */
    public function edit(Color $color): View
    {
        return view('colors.edit', compact('color'));
    }

    /**
     * Atualizar uma cor existente
     */
    public function update(ColorFormRequest $request, Color $color): RedirectResponse
    {
        $color->update($request->validated());

        $url = route('colors.show', ['color' => $color]);
        $htmlMessage = "A cor <a href='$url'><strong>{$color->code}</strong> - '{$color->name}'</a> foi atualizada com sucesso!";

        return redirect()->route('colors.index')
            ->with('alert-type', 'success')
            ->with('alert-msg', $htmlMessage);
    }

    /**
     * Eliminar uma cor (Passo 2 - destroy tem softdeletes)
     */
    public function destroy(Color $color): RedirectResponse
    {
        try {
            // Como o modelo usa SoftDeletes, isto apenas preenche o campo deleted_at automaticamente
            $color->delete();

            return redirect()->route('colors.index')
                ->with('alert-type', 'success')
                ->with('alert-msg', "A cor {$color->name} ({$color->code}) foi eliminada com sucesso!");
        } catch (\Exception $error) {
            return redirect()->back()
                ->with('alert-type', 'danger')
                ->with('alert-msg', "Não foi possível eliminar a cor ({$color->code}) devido a um erro inesperado.");
        }
    }
}
