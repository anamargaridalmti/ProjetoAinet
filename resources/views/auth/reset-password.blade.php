<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
    <script>
        // Sincronização em tempo real com o localStorage da tua Sidebar
        function applySavedTheme() {
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }
        applySavedTheme();

        document.addEventListener('livewire:navigated', applySavedTheme);
    </script>
</head>
<body class="min-h-screen bg-zinc-50 dark:bg-zinc-950 flex flex-col justify-center py-12 sm:px-6 lg:px-8 antialiased">

    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <div class="flex justify-center mb-6">
            <x-app-logo class="h-12 w-auto" />
        </div>
        <flux:heading size="xl" class="font-bold tracking-tight text-zinc-900 dark:text-white">
            Definir Nova Palavra-passe
        </flux:heading>
        <flux:text class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
            Introduza a sua nova credencial de acesso.
        </flux:text>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white dark:bg-zinc-900 py-8 px-4 shadow-xl border border-zinc-200/80 dark:border-zinc-800/80 sm:rounded-xl sm:px-10">
            
            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-900/50 rounded-lg text-sm text-red-600 dark:text-red-400">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('password.update') }}" method="POST" class="space-y-6">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                <flux:field>
                    <flux:label for="email">Endereço de Email</flux:label>
                    <flux:input 
                        type="email" 
                        name="email" 
                        id="email" 
                        value="{{ old('email', request()->email) }}" 
                        required 
                        placeholder="cliente@funshirt.com"
                        icon="envelope"
                    />
                </flux:field>

                <flux:field>
                    <flux:label for="password">Nova Palavra-passe</flux:label>
                    <flux:input 
                        type="password" 
                        name="password" 
                        id="password" 
                        required 
                        placeholder="••••••••"
                        icon="key"
                    />
                </flux:field>

                <flux:field>
                    <flux:label for="password_confirmation">Confirmar Nova Palavra-passe</flux:label>
                    <flux:input 
                        type="password" 
                        name="password_confirmation" 
                        id="password_confirmation" 
                        required 
                        placeholder="••••••••"
                        icon="key"
                    />
                </flux:field>

                <div>
                    <flux:button 
                        type="submit" 
                        variant="filled" 
                        class="w-full justify-center bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-white dark:hover:bg-zinc-100 dark:text-zinc-900 font-semibold py-2.5 cursor-pointer"
                    >
                        Atualizar Palavra-passe
                    </flux:button>
                </div>
            </form>
        </div>
    </div>

    @fluxScripts
</body>
</html>