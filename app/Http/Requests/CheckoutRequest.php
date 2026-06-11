<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
{
    /**
     * Only authenticated users may checkout.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Validation rules.
     *
     * payment_ref format depends on the chosen payment_type:
     *  - Visa    : exactly 16 digits starting with 4
     *  - PayPal  : valid e-mail address
     *  - MB WAY  : exactly 9 digits starting with 9
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $paymentType = $this->input('payment_type');

        $paymentRefRules = match ($paymentType) {
            'Visa'   => ['required', 'string', 'regex:/^4\d{15}$/'],
            'PayPal' => ['required', 'email:rfc'],
            'MB WAY' => ['required', 'string', 'regex:/^9\d{8}$/'],
            default  => ['required', 'string'],
        };

        return [
            'nif'         => ['required', 'string', 'digits:9'],
            'address'     => ['required', 'string', 'min:5', 'max:500'],
            'payment_type' => ['required', Rule::in(['Visa', 'PayPal', 'MB WAY'])],
            'payment_ref' => $paymentRefRules,
            'notes'       => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Human-readable error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nif.required'          => 'O NIF é obrigatório.',
            'nif.digits'            => 'O NIF deve ter exatamente 9 dígitos.',
            'address.required'      => 'O endereço de entrega é obrigatório.',
            'address.min'           => 'O endereço deve ter pelo menos 5 caracteres.',
            'payment_type.required' => 'Selecione um método de pagamento.',
            'payment_type.in'       => 'O método de pagamento selecionado é inválido.',
            'payment_ref.required'  => 'A referência de pagamento é obrigatória.',
            'payment_ref.email'     => 'O e-mail PayPal introduzido não é válido.',
            'payment_ref.regex'     => $this->paymentRefRegexMessage(),
        ];
    }

    /**
     * Returns a contextual error message for the regex rule depending on the type.
     */
    private function paymentRefRegexMessage(): string
    {
        return match ($this->input('payment_type')) {
            'Visa'   => 'O número Visa deve ter 16 dígitos e começar por 4.',
            'MB WAY' => 'O número MB WAY deve ter 9 dígitos e começar por 9.',
            default  => 'A referência de pagamento é inválida.',
        };
    }
}
