<x-layouts::app :title="__('Checkout')">
    <div class="w-full max-w-7xl mx-auto space-y-6">

        {{-- Header --}}
        <div class="border-b border-zinc-200 dark:border-zinc-800 pb-4">
            <flux:heading size="xl" class="font-bold tracking-tight text-zinc-900 dark:text-white">
                🛍️ Finalizar Encomenda
            </flux:heading>
            <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400 mt-0.5">
                Confirme os seus dados e o método de pagamento antes de submeter.
            </flux:text>
        </div>

        {{-- Flash errors --}}
        @if(session('error'))
            <flux:callout variant="danger" icon="x-circle">
                {{ session('error') }}
            </flux:callout>
        @endif

        <form method="POST" action="{{ route('cart.checkout.store') }}" id="checkout-form">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                {{-- ===== LEFT: Form ===== --}}
                <div class="lg:col-span-7 space-y-6">

                    {{-- Billing Data --}}
                    <flux:card class="p-6 space-y-5">
                        <flux:heading size="lg" class="font-bold">📋 Dados de Faturação</flux:heading>

                        <flux:field>
                            <flux:label for="nif">NIF</flux:label>
                            <flux:input
                                id="nif"
                                name="nif"
                                type="text"
                                inputmode="numeric"
                                maxlength="9"
                                placeholder="123456789"
                                value="{{ old('nif', $customer?->nif) }}"
                                required
                            />
                            @error('nif')
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                        </flux:field>

                        <flux:field>
                            <flux:label for="address">Endereço de Entrega</flux:label>
                            <flux:textarea
                                id="address"
                                name="address"
                                rows="3"
                                placeholder="Rua, Número, Código Postal, Cidade"
                                required
                            >{{ old('address', $customer?->address) }}</flux:textarea>
                            @error('address')
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                        </flux:field>

                        <flux:field>
                            <flux:label for="notes">Notas Adicionais</flux:label>
                            <flux:textarea
                                id="notes"
                                name="notes"
                                rows="2"
                                placeholder="Instruções para a entrega, referências, etc. (opcional)"
                            >{{ old('notes') }}</flux:textarea>
                            @error('notes')
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                        </flux:field>
                    </flux:card>

                    {{-- Payment --}}
                    <flux:card class="p-6 space-y-5">
                        <flux:heading size="lg" class="font-bold">💳 Método de Pagamento</flux:heading>

                        <flux:field>
                            <flux:label for="payment_type">Método</flux:label>
                            <flux:select id="payment_type" name="payment_type" required>
                                <option value="" disabled {{ old('payment_type', $customer?->default_payment_type) ? '' : 'selected' }}>
                                    Selecione um método…
                                </option>
                                @foreach(['Visa', 'PayPal', 'MB WAY'] as $method)
                                    <option value="{{ $method }}"
                                        {{ old('payment_type', $customer?->default_payment_type) === $method ? 'selected' : '' }}>
                                        {{ $method }}
                                    </option>
                                @endforeach
                            </flux:select>
                            @error('payment_type')
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                        </flux:field>

                        <flux:field>
                            <flux:label for="payment_ref">Referência de Pagamento</flux:label>
                            <flux:input
                                id="payment_ref"
                                name="payment_ref"
                                type="text"
                                placeholder="Nº de cartão (Visa) • E-mail (PayPal) • Nº de telemóvel (MB WAY)"
                                value="{{ old('payment_ref', $customer?->default_payment_ref) }}"
                                required
                            />
                            <flux:text size="xs" class="text-zinc-400 mt-1">
                                Visa: 16 dígitos começando em 4 &bull;
                                PayPal: e-mail válido &bull;
                                MB WAY: 9 dígitos começando em 9
                            </flux:text>
                            @error('payment_ref')
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                        </flux:field>
                    </flux:card>
                </div>

                {{-- ===== RIGHT: Order Summary ===== --}}
                <div class="lg:col-span-5">
                    <flux:card class="p-6 space-y-4 sticky top-24 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                        <flux:heading size="lg" class="font-bold">Resumo da Encomenda</flux:heading>

                        {{-- Items list --}}
                        <div class="space-y-3 divide-y divide-zinc-100 dark:divide-zinc-800">
                            @foreach($cart as $item)
                                <div class="flex items-center gap-3 pt-3 first:pt-0">
                                    <div class="w-12 h-12 shrink-0 rounded-lg bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 overflow-hidden flex items-center justify-center">
                                        @if($item['image_type'] === 'own')
                                            <img src="{{ url('/img-profiles/' . $item['image_url']) }}"
                                                alt="{{ $item['name'] }}"
                                                class="w-full h-full object-contain">
                                        @else
                                            <img src="{{ url('/images/catalog/' . $item['image_url']) }}"
                                                alt="{{ $item['name'] }}"
                                                class="w-full h-full object-contain">
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-zinc-900 dark:text-white truncate">
                                            {{ $item['name'] }}
                                        </p>
                                        <p class="text-xs text-zinc-400">
                                            {{ $item['color_name'] }} · {{ $item['size'] }} · {{ $item['qty'] }} un.
                                        </p>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <p class="text-sm font-bold text-zinc-900 dark:text-white">
                                            {{ number_format($item['subtotal'], 2, ',', '.') }}€
                                        </p>
                                        <p class="text-[10px] text-zinc-400">
                                            {{ number_format($item['unit_price'], 2, ',', '.') }}€/un
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Total --}}
                        <div class="border-t border-zinc-200 dark:border-zinc-800 pt-4 flex justify-between items-baseline">
                            <span class="text-zinc-600 dark:text-zinc-400 font-medium">Total a pagar:</span>
                            <span class="text-3xl font-black text-zinc-900 dark:text-white">
                                {{ number_format($total, 2, ',', '.') }}€
                            </span>
                        </div>

                        {{-- Actions --}}
                        <div class="pt-2 space-y-2">
                            <button
                                type="submit"
                                id="submit-checkout"
                                class="w-full inline-flex items-center justify-center px-4 py-3 bg-zinc-950 hover:bg-zinc-800 text-white dark:bg-white dark:hover:bg-zinc-100 dark:text-zinc-900 font-bold rounded-xl shadow-sm transition text-sm cursor-pointer">
                                ✅ Confirmar Encomenda
                            </button>
                            <a href="{{ route('cart.show') }}"
                                class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-200 text-sm font-semibold rounded-xl transition text-center cursor-pointer">
                                ← Voltar ao Carrinho
                            </a>
                        </div>
                    </flux:card>
                </div>

            </div>
        </form>

    </div>
</x-layouts::app>
