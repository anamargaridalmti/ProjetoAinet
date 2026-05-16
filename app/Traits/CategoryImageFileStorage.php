<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait CategoryImageFileStorage
{
    /**
     * Faz o upload de uma imagem da categoria e remove a antiga, se aplicável.
     */
    public function saveCategoryImageFile(UploadedFile $file, ?string $oldFileName = null): string
    {
        // 1. Se existir uma imagem antiga, apaga-a do disco público
        if ($oldFileName) {
            $this->deleteCategoryImageFile($oldFileName);
        }

        // 2. Guarda o novo ficheiro na pasta 'categories' do disco 'public'
        $path = $file->store('categories', 'public');

        // 3. Retorna apenas o nome do ficheiro (ex: abc123xyz.jpg) para guardar na BD
        return basename($path);
    }

    /**
     * Apaga o ficheiro de imagem do armazenamento.
     */
    public function deleteCategoryImageFile(string $fileName): bool
    {
        $filePath = 'categories/' . $fileName;

        if (Storage::disk('public')->exists($filePath)) {
            return Storage::disk('public')->delete($filePath);
        }

        return false;
    }
}
