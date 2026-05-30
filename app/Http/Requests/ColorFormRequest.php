<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ColorFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $imageRule = $this->isMethod('post') ? 'required|image|max:2048' : 'nullable|image|max:2048';

        return [
            'code' => $this->isMethod('post') ? 'required|string|size:6|unique:colors,code' : 'nullable',
            'name' => 'required|string|max:255',
            'tshirt_image' => $imageRule,
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'O código hexadecimal da cor é obrigatório.',
            'code.size' => 'O código deve ter exatamente 6 caracteres (sem o #).',
            'code.unique' => 'Este código de cor já se encontra registado.',
            'name.required' => 'O nome descritivo da cor é obrigatório.',
            'tshirt_image.required' => 'É obrigatório fazer o upload do ficheiro de imagem da t-shirt base.',
            'tshirt_image.image' => 'O ficheiro selecionado tem de ser uma imagem válida.',
            'tshirt_image.max' => 'A imagem da t-shirt base não pode ter mais de 2MB.',
        ];
    }
}
