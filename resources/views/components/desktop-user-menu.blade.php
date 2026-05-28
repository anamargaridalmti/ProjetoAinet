<flux:dropdown position="bottom" align="start">
    <div class="text-zinc-800 dark:text-zinc-200 font-medium">
        <flux:sidebar.profile
            :name="auth()->user()->name"
            :initials="auth()->user()->initials()"
            :avatar="auth()->user()->photo_url && file_exists(storage_path('app/public/profiles/' . auth()->user()->photo_url)) ? url('img-profiles/' . auth()->user()->photo_url) . '?v=' . time() : null"
            icon:trailing="chevrons-up-down"
            data-test="sidebar-menu-button"
        />
    </div>

    <flux:menu>
        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
            <flux:avatar
                :name="auth()->user()->name"
                :initials="auth()->user()->initials()"
                :src="auth()->user()->photo_url && file_exists(storage_path('app/public/profiles/' . auth()->user()->photo_url)) ? url('img-profiles/' . auth()->user()->photo_url) . '?v=' . time() : null"
            />
            <div class="grid flex-1 text-start text-sm leading-tight">
                <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
            </div>
        </div>

        <flux:menu.separator />

        <flux:menu.radio.group>
            <flux:menu.item icon="shopping-cart" :href="route('cart.show')" :current="request()->routeIs('cart.show')" wire:navigate>
                O meu Carrinho
            </flux:menu.item>

            <flux:menu.item icon="document-text" :href="route('home')" :current="false" wire:navigate>
                Minhas Encomendas
            </flux:menu.item>

            <flux:menu.item icon="heart" :href="route('home')" :current="false" wire:navigate>
                Favoritos
            </flux:menu.item>
        </flux:menu.radio.group>

        <flux:menu.separator />

        <flux:menu.radio.group>
            <flux:menu.item :href="route('dashboard')" icon="home" :current="request()->routeIs('dashboard')" wire:navigate>
                Voltar ao Dashboard
            </flux:menu.item>

            <flux:menu.item :href="route('profile.edit')" icon="cog" :current="request()->routeIs('profile.edit')" wire:navigate>
                {{ __('Settings') }}
            </flux:menu.item>
        </flux:menu.radio.group>

        <flux:menu.separator />

        <flux:menu.radio.group>
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <flux:menu.item
                    as="button"
                    type="submit"
                    icon="arrow-right-start-on-rectangle"
                    class="w-full cursor-pointer"
                    data-test="logout-button"
                >
                    {{ __('Log out') }}
                </flux:menu.item>
            </form>
        </flux:menu.radio.group>
    </flux:menu>
</flux:dropdown>