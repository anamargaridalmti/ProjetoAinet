<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
    <script>
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
            Entrar na FunShirt
        </flux:heading>
        <flux:text class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
            Inicie sessão para continuar as suas compras.
        </flux:text>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white dark:bg-zinc-900 py-8 px-4 shadow-xl border border-zinc-200/80 dark:border-zinc-800/80 sm:rounded-xl sm:px-10">

            {{-- Status message (e.g. after password reset) --}}
            <x-auth-session-status class="mb-4" :status="session('status')" />

            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-900/50 rounded-lg text-sm text-red-600 dark:text-red-400">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-6">
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
                        placeholder="exemplo@mail.pt"
                        icon="envelope"
                    />
                </flux:field>

                <flux:field>
                    <div class="flex items-center justify-between">
                        <flux:label for="password">Palavra-passe</flux:label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                               class="text-xs text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300">
                                Esqueceu a palavra-passe?
                            </a>
                        @endif
                    </div>
                    <flux:input
                        type="password"
                        name="password"
                        id="password"
                        required
                        placeholder="••••••••"
                        icon="key"
                    />
                </flux:field>

                <div class="flex items-center">
                    <input type="checkbox" name="remember" id="remember"
                        class="h-4 w-4 rounded border-zinc-300 text-zinc-900 focus:ring-zinc-500">
                    <label for="remember" class="ml-2 block text-sm text-zinc-600 dark:text-zinc-400">
                        Lembrar-me neste dispositivo
                    </label>
                </div>

                <div>
                    <flux:button
                        type="submit"
                        variant="filled"
                        class="w-full justify-center bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-white dark:hover:bg-zinc-100 dark:text-zinc-900 font-semibold py-2.5 cursor-pointer"
                    >
                        Entrar
                    </flux:button>
                </div>
            </form>

            <div class="mt-6 pt-6 border-t border-zinc-100 dark:border-zinc-800/80 text-center space-y-3">
                <p class="text-xs text-zinc-500 dark:text-zinc-400">
                    Ainda não tem conta?
                </p>
                <flux:button
                    variant="subtle"
                    :href="route('register')"
                    wire:navigate
                    class="w-full justify-center text-xs font-semibold"
                >
                    Criar Conta Gratuita
                </flux:button>
            </div>

        </div>
    </div>

    @fluxScripts
</body>
</html>