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
            Recuperar Palavra-passe
        </flux:heading>
        <flux:text class="mt-2 text-sm text-zinc-600 dark:text-zinc-400 px-4">
            Introduza o seu e-mail de registo. Enviaremos um link seguro para redefinir a sua senha.
        </flux:text>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white dark:bg-zinc-900 py-8 px-4 shadow-xl border border-zinc-200/80 dark:border-zinc-800/80 sm:rounded-xl sm:px-10">
            
            @if (session('status'))
                <div class="mb-4 p-3 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-900/50 rounded-lg text-sm text-emerald-600 dark:text-emerald-400">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-900/50 rounded-lg text-sm text-red-600 dark:text-red-400">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('password.email') }}" method="POST" class="space-y-6">
                @csrf

                <flux:field>
                    <flux:label for="email">Endereço de Email</flux:label>
                    <flux:input 
                        type="email" 
                        name="email" 
                        id="email" 
                        value="{{ old('email') }}" 
                        required 
                        autofocus 
                        placeholder="cliente@mail.pt"
                        icon="envelope"
                    />
                </flux:field>

                <div class="flex flex-col gap-3">
                    <flux:button 
                        type="submit" 
                        variant="filled" 
                        class="w-full justify-center bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-white dark:hover:bg-zinc-100 dark:text-zinc-900 font-semibold py-2.5 cursor-pointer"
                    >
                        Enviar Link de Recuperação
                    </flux:button>

                    <flux:button 
                        :href="route('login')" 
                        variant="subtle" 
                        class="w-full justify-center"
                        wire:navigate
                    >
                        Voltar ao Login
                    </flux:button>
                </div>
            </form>
        </div>
    </div>

    @fluxScripts
</body>
</html>