<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" id="theme-html">
    <head>
        @include('partials.head')
        <script>
            // Executa imediatamente antes de renderizar para evitar flickering
            if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <x-app-logo :href="route('home')" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            @if (count(session('cart', [])) > 0)
                <flux:sidebar.nav variant="outline">
                    <div class="relative inline-flex items-center mr-4">
                        <div class="-top-0.5 absolute left-6 z-10">
                            <p class="flex p-3 h-3 w-3 items-center justify-center rounded-full bg-red-500 text-xs text-white">
                                {{ count(session('cart', [])) }}
                            </p>
                        </div>
                        <flux:navlist.item icon="shopping-cart" icon:variant="solid" :href="route('cart.show')" :current="request()->routeIs('cart.show')" wire:navigate>
                            <span class="pl-2">Shopping Cart</span>
                        </flux:navlist.item>
                    </div>
                </flux:sidebar.nav>
            @endif

            <flux:sidebar.nav>
                <flux:sidebar.group heading="Shop" class="grid">
                    <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="squares-2x2" :href="route('home')" :current="request()->routeIs('home')" wire:navigate>
                        Catálogo
                    </flux:sidebar.item>
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:sidebar.nav>
                <flux:sidebar.group heading="Management" class="grid">
                    <flux:sidebar.item icon="swatch" :href="route('colors.index')" :current="request()->routeIs('colors.index')" wire:navigate>
                        Cores
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="tag" :href="route('categories.index')" :current="request()->routeIs('categories.index')" wire:navigate>
                        Categorias
                    </flux:sidebar.item>
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

            <flux:sidebar.nav>
                <button onclick="toggleTheme()" type="button" class="flex w-full items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200/50 dark:hover:bg-zinc-800/50 transition-colors cursor-pointer">
                    <svg id="theme-icon-sun" class="hidden dark:block h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M16.243 17.657l.707.707M6.343 6.343l.707-.707m2.828 9.9a5 5 0 117.072 0l-.707-.707A3.5 3.5 0 108.344 11.5l-.707.707z" />
                    </svg>
                    <svg id="theme-icon-moon" class="block dark:hidden h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    <span id="theme-text-light" class="block dark:hidden">Modo Escuro</span>
                    <span id="theme-text-dark" class="hidden dark:block">Modo Claro</span>
                </button>
            </flux:sidebar.nav>

            @auth
                <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
            @else
                <flux:sidebar.item icon="user" :href="route('login')" :current="request()->routeIs('login')" wire:navigate>
                    Login
                </flux:sidebar.item>
            @endauth
        </flux:sidebar>

        @auth
            <flux:header class="lg:hidden">
                <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
                <flux:spacer />
                <flux:dropdown position="top" align="end">
                    <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />
                    <flux:menu>
                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full cursor-pointer">
                                {{ __('Log out') }}
                            </flux:menu.item>
                        </form>
                    </flux:menu>
                </flux:dropdown>
            </flux:header>
        @endauth

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts

        <script>
            function toggleTheme() {
                const html = document.documentElement;
                if (html.classList.contains('dark')) {
                    html.classList.remove('dark');
                    localStorage.setItem('theme', 'light');
                } else {
                    html.classList.add('dark');
                    localStorage.setItem('theme', 'dark');
                }
            }
        </script>
    </body>
</html>