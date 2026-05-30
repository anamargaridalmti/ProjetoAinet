<x-layouts::app :title="__('Criar Staff')">
    <div class="max-w-2xl mx-auto space-y-6">
        
        <div class="flex items-center gap-4 mb-2">
            <flux:button 
                icon="arrow-left" 
                variant="ghost" 
                :href="route('admin.users.index')" 
                wire:navigate 
                aria-label="Voltar"
                class="cursor-pointer"
            />
            <div>
                <flux:heading size="xl" class="font-bold tracking-tight text-zinc-900 dark:text-white">
                    Criar Novo Membro do Staff
                </flux:heading>
                <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">
                    Registe um novo Funcionário ou Administrador para a equipa da FunShirt.
                </flux:text>
            </div>
        </div>

        <flux:card class="p-6 bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800/80 shadow-md sm:rounded-xl">
            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-5">
                @csrf

                <flux:field>
                    <flux:label for="name">Nome Completo</flux:label>
                    <flux:input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="{{ old('name') }}" 
                        required 
                        autofocus
                        placeholder="Ex: Carlos Antunes"
                        icon="user"
                    />
                    <flux:error name="name" />
                </flux:field>

                <flux:field>
                    <flux:label for="email">Endereço de E-mail</flux:label>
                    <flux:input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="{{ old('email') }}" 
                        required 
                        placeholder="colaborador@funshirt.com"
                        icon="envelope"
                    />
                    <flux:error name="email" />
                </flux:field>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label for="user_type">Perfil de Acesso</flux:label>
                        <flux:select id="user_type" name="user_type" placeholder="Selecione o perfil..." required>
                            <flux:select.option value="F" {{ old('user_type') == 'F' ? 'selected' : '' }}>Funcionário (Staff)</flux:select.option>
                            <flux:select.option value="A" {{ old('user_type') == 'A' ? 'selected' : '' }}>Administrador (Total)</flux:select.option>
                        </flux:select>
                        <flux:error name="user_type" />
                    </flux:field>

                    <flux:field>
                        <flux:label for="gender">Género</flux:label>
                        <flux:select id="gender" name="gender" placeholder="Selecione o género..." required>
                            <flux:select.option value="M" {{ old('gender') == 'M' ? 'selected' : '' }}>Masculino</flux:select.option>
                            <flux:select.option value="F" {{ old('gender') == 'F' ? 'selected' : '' }}>Feminino</flux:select.option>
                        </flux:select>
                        <flux:error name="gender" />
                    </flux:field>
                </div>

                <flux:separator class="my-2" />

                <flux:field>
                    <flux:label for="password">Palavra-passe Provisória</flux:label>
                    <flux:input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required 
                        placeholder="Mínimo 3 caracteres"
                        icon="key"
                    />
                    <flux:error name="password" />
                </flux:field>

                <flux:field>
                    <flux:label for="password_confirmation">Confirmar Palavra-passe</flux:label>
                    <flux:input 
                        type="password" 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        required 
                        placeholder="Repita a palavra-passe"
                        icon="key"
                    />
                </flux:field>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                    <flux:button 
                        :href="route('admin.users.index')" 
                        variant="ghost" 
                        wire:navigate
                        class="cursor-pointer"
                    >
                        Cancelar
                    </flux:button>
                    
                    <flux:button 
                        type="submit" 
                        variant="filled" 
                        class="bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-white dark:hover:bg-zinc-100 dark:text-zinc-900 font-semibold cursor-pointer"
                    >
                        Gravar Conta
                    </flux:button>
                </div>
            </form>
        </flux:card>
    </div>
</x-layouts::app>