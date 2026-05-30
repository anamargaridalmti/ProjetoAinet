<x-layouts::app :title="__('O Meu Perfil')">
    <div class="max-w-2xl mx-auto space-y-6">
        
        <div class="flex items-center justify-between">
            <flux:button 
                icon="arrow-left" 
                variant="subtle" 
                :href="route('catalog.index')" 
                wire:navigate
            >
                Voltar ao Catálogo
            </flux:button>
        </div>

        <div class="flex justify-between items-center border-b border-zinc-200 dark:border-zinc-800 pb-4">
            <div>
                <flux:heading size="xl" class="font-bold tracking-tight text-zinc-900 dark:text-white">
                    👤 O Meu Perfil (Cliente)
                </flux:heading>
                <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400 mt-0.5">
                    Gira as tuas informações de conta e fotografia de avatar (G1).
                </flux:text>
            </div>
        </div>

        @if(session('status'))
            <flux:card class="p-3 border text-sm bg-emerald-50 dark:bg-emerald-950/30 border-emerald-200 text-emerald-700 dark:text-emerald-400">
                {{ session('status') }}
            </flux:card>
        @endif

        <flux:card class="space-y-6 bg-white dark:bg-zinc-900">
            <div>
                <flux:heading size="lg" class="font-semibold">Alterar Dados Pessoais</flux:heading>
            </div>

            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <flux:field>
                    <flux:label for="name">Nome Completo</flux:label>
                    <flux:input id="name" type="text" name="name" value="{{ old('name', Auth::user()->name) }}" required />
                    @error('name') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                </flux:field>

                <flux:field>
                    <flux:label for="email">Endereço de E-mail</flux:label>
                    <flux:input id="email" type="email" name="email" value="{{ old('email', Auth::user()->email) }}" required />
                    @error('email') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                </flux:field>

                <flux:field>
                    <flux:label for="photo">Fotografia / Avatar</flux:label>
                    
                    <div class="my-3">
                        @if(Auth::user()->photo_url && \Illuminate\Support\Facades\Storage::disk('public')->exists('profiles/' . Auth::user()->photo_url))
                            <img 
                                src="{{ url('img-profiles/' . Auth::user()->photo_url) }}?v={{ time() }}" 
                                alt="Avatar" 
                                class="w-24 h-24 rounded-full object-cover border-2 border-zinc-200 dark:border-zinc-700 shadow-sm"
                            >
                        @else
                            <div class="w-24 h-24 rounded-full bg-zinc-100 dark:bg-zinc-800 border-2 border-dashed border-zinc-300 dark:border-zinc-700 flex items-center justify-center text-3xl shadow-2xs text-zinc-400 dark:text-zinc-500 font-bold">
                                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                            </div>
                        @endif
                    </div>

                    <flux:input 
                        id="photo" 
                        type="file" 
                        name="photo" 
                        accept="image/*" 
                        class="file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-zinc-900 file:text-white dark:file:bg-zinc-100 dark:file:text-zinc-900 cursor-pointer"
                    />
                    @error('photo') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                </flux:field>

                <div class="flex justify-end pt-2">
                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-white dark:hover:bg-zinc-100 dark:text-zinc-900 text-sm font-semibold rounded-lg shadow-sm transition cursor-pointer">
                        Guardar Alterações
                    </button>
                </div>
            </form>
        </flux:card>

        <flux:card class="space-y-6 bg-white dark:bg-zinc-900">
            <div>
                <flux:heading size="lg" class="font-semibold">Alterar Palavra-passe</flux:heading>
            </div>

            <form method="POST" action="/user/password" class="space-y-6">
                @csrf
                @method('PUT')

                <flux:field>
                    <flux:label for="current_password">Palavra-passe Atual</flux:label>
                    <flux:input id="current_password" type="password" name="current_password" required />
                    @error('current_password', 'updatePassword') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                </flux:field>

                <flux:field>
                    <flux:label for="password">Nova Palavra-passe</flux:label>
                    <flux:input id="password" type="password" name="password" required />
                    @error('password', 'updatePassword') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                </flux:field>

                <flux:field>
                    <flux:label for="password_confirmation">Confirmar Nova Palavra-passe</flux:label>
                    <flux:input id="password_confirmation" type="password" name="password_confirmation" required />
                </flux:field>

                <div class="flex justify-end pt-2">
                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-white dark:hover:bg-zinc-100 dark:text-zinc-900 text-sm font-semibold rounded-lg shadow-sm transition cursor-pointer">
                        Atualizar Senha
                    </button>
                </div>
            </form>
        </flux:card>

    </div>
</x-layouts::app>