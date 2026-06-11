<?php

namespace App\Http\Controllers;

use App\Models\TshirtImage;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CustomerTshirtImageController extends Controller
{
    // Listar as imagens privadas do cliente autenticado
    public function index(): View
    {
        $tshirtImages = TshirtImage::where('customer_id', auth()->user()->id)->latest()->paginate(12);
        return view('customer.tshirt-images.index', compact('tshirtImages'));
    }

    public function create(): View
    {
        return view('customer.tshirt-images.create');
    }

    // Guardar a imagem na pasta privada
    public function store(Request $request): RedirectResponse
    {
        if (auth()->user()->user_type !== 'C') {
            abort(403, 'Apenas clientes podem guardar imagens personalizadas.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image_file' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $tshirtImage = new TshirtImage();
        $tshirtImage->name = $request->name;
        $tshirtImage->description = $request->description;
        $tshirtImage->category_id = null; // Imagens personalizadas não têm categoria
        $tshirtImage->customer_id = auth()->user()->id;

        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = uniqid('own_') . '.' . $file->getClientOriginalExtension();

            // Guarda na diretoria privada: storage/app/private/tshirt_images_private
            $file->storeAs('private/tshirt_images_private', $filename);
            $tshirtImage->image_url = $filename;
        }

        $tshirtImage->save();

        return redirect()->route('customer.tshirt-images.index')
            ->with('alert-type', 'success')
            ->with('alert-msg', 'Imagem personalizada adicionada com sucesso!');
    }

    public function edit(TshirtImage $tshirtImage): View
    {
        if (auth()->user()->id !== $tshirtImage->customer_id) {
            abort(403);
        }
        return view('customer.tshirt-images.edit', compact('tshirtImage'));
    }

    public function update(Request $request, TshirtImage $tshirtImage): RedirectResponse
    {
        if (auth()->user()->id !== $tshirtImage->customer_id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $tshirtImage->name = $request->name;
        $tshirtImage->description = $request->description;

        if ($request->hasFile('image_file')) {
            // Remove o ficheiro físico antigo da diretoria privada
            $oldPath = storage_path('app/private/tshirt_images_private/' . $tshirtImage->image_url);
            if (file_exists($oldPath)) {
                @unlink($oldPath);
            }

            $file = $request->file('image_file');
            $filename = uniqid('own_') . '.' . $file->getClientOriginalExtension();
            $file->storeAs('private/tshirt_images_private', $filename);
            $tshirtImage->image_url = $filename;
        }

        $tshirtImage->save();

        return redirect()->route('customer.tshirt-images.index')
            ->with('alert-type', 'success')
            ->with('alert-msg', 'Imagem personalizada atualizada com sucesso!');
    }

    public function destroy(TshirtImage $tshirtImage): RedirectResponse
    {
        if (auth()->user()->id !== $tshirtImage->customer_id) {
            abort(403);
        }

        // Remove o ficheiro físico
        $path = storage_path('app/private/tshirt_images_private/' . $tshirtImage->image_url);
        if (file_exists($path)) {
            @unlink($path);
        }

        // Executa o Soft Delete nativo (conforme configurado no teu modelo)
        $tshirtImage->delete();

        return redirect()->route('customer.tshirt-images.index')
            ->with('alert-type', 'success')
            ->with('alert-msg', 'Imagem personalizada removida da sua conta.');
    }

    // Método que serve o ficheiro privado de forma controlada
    public function showPrivateImage($filename)
    {
        $tshirtImage = TshirtImage::where('image_url', $filename)->firstOrFail();

        // Bloqueia se o utilizador logado for um cliente comum e NÃO for o dono da imagem
        if (auth()->user()->user_type === 'C' && auth()->user()->id !== $tshirtImage->customer_id) {
            abort(403, 'Não tem permissão para visualizar esta imagem privada.');
        }

        $path = storage_path('app/private/tshirt_images_private/' . $filename);
        if (!file_exists($path)) {
            abort(404);
        }

        $file = file_get_contents($path);
        $type = mime_content_type($path);

        return response($file, 200)->header("Content-Type", $type);
    }
}