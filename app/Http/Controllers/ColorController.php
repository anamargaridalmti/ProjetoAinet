<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Color;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\ColorFormRequest;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Storage;

class ColorController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                if (!auth()->check() || auth()->user()->user_type !== 'A') {
                    abort(403, 'Acesso restrito aos administradores da plataforma.');
                }
                return $next($request);
            }),
        ];
    }

    public function index(): View
    {
        $colors = Color::orderBy('name')->paginate(20);
        return view('admin.colors.index', compact('colors'));
    }

    public function show(Color $color): View
    {
        return view('admin.colors.show', compact('color'));
    }

    public function create(): View
    {
        $color = new Color();
        return view('admin.colors.create', compact('color'));
    }

    public function store(ColorFormRequest $request): RedirectResponse
    {
        $formData = $request->validated();
        $codeUpper = strtoupper($formData['code']);

        $newColor = new Color();
        $newColor->code = $codeUpper;
        $newColor->name = $formData['name'];

        if ($request->hasFile('tshirt_image')) {
            $file = $request->file('tshirt_image');
            $filename = $codeUpper . '.' . $file->getClientOriginalExtension();
            $file->storeAs('tshirt_base', $filename, 'public');
        }

        $newColor->save();

        $url = route('colors.show', ['color' => $newColor->code]);
        $htmlMessage = "A cor <a href='$url'><strong>{$newColor->code}</strong> - '{$newColor->name}'</a> foi criada com sucesso!";

        return redirect()->route('colors.index')
            ->with('alert-type', 'success')
            ->with('alert-msg', $htmlMessage);
    }

    public function edit(Color $color): View
    {
        return view('admin.colors.edit', compact('color'));
    }

    public function update(ColorFormRequest $request, Color $color): RedirectResponse
    {
        $formData = $request->validated();
        $color->name = $formData['name'];

        if ($request->hasFile('tshirt_image')) {
            $file = $request->file('tshirt_image');
            $filename = strtoupper($color->code) . '.' . $file->getClientOriginalExtension();

            $oldPath = 'tshirt_base/' . $filename;
            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }

            $file->storeAs('tshirt_base', $filename, 'public');
        }

        $color->save();

        $url = route('colors.show', ['color' => $color->code]);
        $htmlMessage = "A cor <a href='$url'><strong>{$color->code}</strong> - '{$color->name}'</a> foi atualizada com sucesso!";

        return redirect()->route('colors.index')
            ->with('alert-type', 'success')
            ->with('alert-msg', $htmlMessage);
    }

    public function destroy(Color $color): RedirectResponse
    {
        try {
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
