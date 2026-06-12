<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" id="theme-html">
    <head>
        @include('partials.head')
        <script>
            if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>
    </head>
    <body class="min-h-screen bg-zinc-50 dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 antialiased">

        @php 
            $cartCount = count(session('cart', [])); 
            $favCount = count(session('favorites', [])); // Preparado para o G3
            $isAdmin = auth()->check() && auth()->user()->user_type === 'A';
            $user = auth()->user();
        @endphp

        <header class="w-full bg-zinc-50 dark:bg-zinc-950 border-b border-zinc-200 dark:border-zinc-800 px-6 py-4 sticky top-0 z-40 backdrop-blur-md bg-opacity-90 dark:bg-opacity-90">
            <div class="max-w-7xl mx-auto flex items-center justify-between">
                
                <div class="flex items-center gap-3">
                    <x-app-logo :href="route('home')" wire:navigate />
                    <div>
                        <h1 class="text-lg font-black text-zinc-900 dark:text-white tracking-tight">FunShirt</h1>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 font-medium">
                            {{ $isAdmin ? 'Painel Administrativo' : 'A tua loja de T-Shirts' }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 sm:gap-4">

                    <a href="{{ route('home') }}" wire:navigate class="p-2 text-zinc-500 dark:text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200 transition" title="Página Inicial">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9.75L12 3l9 6.75V21a.75.75 0 01-.75.75H15a.75.75 0 01-.75-.75v-4.5a.75.75 0 00-.75-.75h-3a.75.75 0 00-.75.75V21a.75.75 0 01-.75.75H3.75A.75.75 0 013 21V9.75z" />
                        </svg>
                    </a>

                    <a href="#" class="relative p-2 text-zinc-500 dark:text-zinc-400 hover:text-red-500 dark:hover:text-red-400 transition" title="Os Meus Favoritos">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                        @if($favCount > 0)
                            <span class="absolute top-1 right-1 bg-red-500 text-white text-[9px] font-extrabold rounded-full h-4 w-4 flex items-center justify-center shadow-xs">
                                {{ $favCount }}
                            </span>
                        @endif
                    </a>

                    <a href="{{ route('cart.show') }}" wire:navigate class="relative p-2 transition {{ $cartCount > 0 ? 'text-zinc-900 dark:text-white' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200' }}" title="Carrinho de Compras">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span class="absolute top-1 right-1 text-white text-[9px] font-extrabold rounded-full h-4 w-4 flex items-center justify-center shadow-xs {{ $cartCount > 0 ? 'bg-emerald-500' : 'bg-zinc-400 dark:bg-zinc-600' }}">
                            {{ $cartCount }}
                        </span>
                    </a>

                    <button onclick="toggleTheme()" type="button" class="p-2 rounded-lg text-zinc-500 dark:text-zinc-400 hover:bg-zinc-200/50 dark:hover:bg-zinc-800/50 transition cursor-pointer">
                        <svg id="theme-icon-sun" class="hidden dark:block h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M16.243 17.657l.707.707M6.343 6.343l.707-.707m2.828 9.9a5 5 0 117.072 0l-.707-.707A3.5 3.5 0 108.344 11.5l-.707.707z" />
                        </svg>
                        <svg id="theme-icon-moon" class="block dark:hidden h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </button>

                    <div class="h-5 w-px bg-zinc-200 dark:bg-zinc-800"></div>

                    @auth
                        <flux:dropdown position="bottom" align="end">
                            
                            <flux:button variant="subtle" class="!p-1 hover:opacity-80 transition flex items-center gap-1 cursor-pointer rounded-full bg-transparent dark:bg-transparent border-none">
                                <div class="w-8 h-8 rounded-full bg-zinc-200 dark:bg-zinc-800 overflow-hidden border border-zinc-300 dark:border-zinc-700 flex items-center justify-center text-xs font-bold text-zinc-700 dark:text-zinc-300 shadow-3xs">
                                    @if($user->photo_url && \Illuminate\Support\Facades\Storage::disk('public')->exists('profiles/' . $user->photo_url))
                                        <img src="{{ url('/img-profiles/' . $user->photo_url) }}?v={{ time() }}" alt="Avatar" class="w-full h-full object-cover">
                                    @else
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    @endif
                                </div>
                                <svg class="w-3 h-3 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </flux:button>

                            <flux:menu class="w-52">
                                <div class="px-3 py-2 border-b border-zinc-100 dark:border-zinc-800 mb-1">
                                    <p class="text-xs font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Sessão Iniciada</p>
                                    <p class="text-sm font-bold text-zinc-800 dark:text-zinc-200 truncate">{{ $user->name }}</p>
                                </div>
                                
                                @if($user->user_type !== 'E')
                                    <flux:menu.item :href="route('profile.edit')" icon="user" wire:navigate class="cursor-pointer">
                                        O Meu Perfil
                                    </flux:menu.item>

                                    @if($user->user_type === 'C')
                                        <flux:menu.item :href="route('orders.index')" icon="shopping-bag" wire:navigate class="cursor-pointer">
                                            As Minhas Encomendas
                                        </flux:menu.item>

                                        <flux:menu.item :href="route('customer.tshirt-images.index')" icon="photo" wire:navigate class="cursor-pointer">
                                            As Minhas Imagens
                                        </flux:menu.item>
                                    @endif

                                    @if(in_array($user->user_type, ['F', 'A']))
                                        <flux:menu.item :href="route('admin.orders.index')" icon="clipboard-document-list" wire:navigate class="cursor-pointer">
                                            Gestão de Encomendas
                                        </flux:menu.item>
                                    @endif
                                @endif

                                <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
                                    @csrf
                                </form>

                                <flux:menu.item 
                                    as="button" 
                                    type="submit" 
                                    form="logout-form" 
                                    icon="arrow-right-start-on-rectangle" 
                                    class="w-full text-red-600 hover:text-red-700 cursor-pointer"
                                >
                                    Terminar Sessão
                                </flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>
                    @else
                        <a href="{{ route('login') }}" wire:navigate class="text-sm font-semibold text-zinc-700 dark:text-zinc-200 hover:text-zinc-950 dark:hover:text-white transition">
                            Entrar / Login
                        </a>
                    @endauth

                </div>
            </div>
        </header>

        <div class="flex min-h-[calc(100vh-73px)] w-full">
            
            @if($isAdmin)
                <aside class="w-64 border-e border-zinc-200 bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-950 p-4 shrink-0 hidden md:block">
                    <flux:sidebar.nav class="space-y-6">
                        <flux:sidebar.group heading="Administração da Loja" class="grid gap-1">
                            <flux:sidebar.item icon="squares-2x2" :href="route('catalog.index')" :current="request()->routeIs('catalog.*')" wire:navigate>
                                Ver Catálogo
                            </flux:sidebar.item>
                            <flux:sidebar.item icon="swatch" :href="route('colors.index')" :current="request()->routeIs('colors.index')" wire:navigate>
                                Cores
                            </flux:sidebar.item>
                            <flux:sidebar.item icon="tag" :href="route('categories.index')" :current="request()->routeIs('categories.index')" wire:navigate>
                                Categorias
                            </flux:sidebar.item>
                            <flux:sidebar.item icon="photo" :href="route('admin.tshirt-images.index')" :current="request()->routeIs('admin.tshirt-images.*')" wire:navigate>
                                Estampagem
                            </flux:sidebar.item>
                            <flux:sidebar.item icon="currency-euro" :href="route('admin.prices.edit')" :current="request()->routeIs('admin.prices.*')" wire:navigate>
                                Preços
                            </flux:sidebar.item>
                            <flux:sidebar.item :href="route('admin.users.index')" icon="users" :current="request()->routeIs('admin.users.*')" wire:navigate>
                                Utilizadores
                            </flux:sidebar.item>

                            <flux:sidebar.item icon="clipboard-document-list" :href="route('admin.orders.index')" :current="request()->routeIs('admin.orders.*')" wire:navigate>
                                Gestão de Encomendas
                            </flux:sidebar.item>
                            
                            <flux:sidebar.item icon="chart-bar" :href="route('admin.statistics')" :current="request()->routeIs('admin.statistics')" wire:navigate>
                                Estatísticas
                            </flux:sidebar.item>
                        </flux:sidebar.group>
                    </flux:sidebar.nav>
                </aside>
            @endif

            <main class="flex-1 p-6 md:p-8 max-w-7xl mx-auto w-full">
                {{ $slot }}
            </main>

        </div>

        <flux:toast.group>
            <flux:toast />
        </flux:toast.group>

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