<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-zinc-50 dark:bg-zinc-950 flex flex-col justify-center py-12 sm:px-6 lg:px-8 antialiased">
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <flux:heading size="xl" class="font-bold tracking-tight text-zinc-900 dark:text-white">
            Verifique o seu e-mail
        </flux:heading>
        <flux:text class="mt-4 px-4">
            Obrigado por se registar na FunShirt! Antes de começar a comprar, por favor valide a sua conta clicando no link que acabámos de enviar para o seu e-mail.
        </flux:text>

        @if (session('message'))
            <div class="mt-4 mx-4 p-3 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-900/50 rounded-lg text-sm text-emerald-600 dark:text-emerald-400">
                {{ session('message') }}
            </div>
        @endif

        <div class="mt-8 bg-white dark:bg-zinc-900 py-6 px-4 shadow-xl border border-zinc-200/80 dark:border-zinc-800/80 sm:rounded-xl sm:px-10 flex flex-col gap-4">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <flux:button type="submit" variant="filled" class="w-full justify-center bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 cursor-pointer">
                    Reenviar E-mail de Verificação
                </flux:button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <flux:button type="submit" variant="subtle" class="w-full justify-center cursor-pointer">
                    Efetuar Logout
                </flux:button>
            </form>
        </div>
    </div>
    @fluxScripts
</body>
</html>