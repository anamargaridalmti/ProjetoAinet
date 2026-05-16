<x-layouts::main-content :title="__('colors')"
                        heading="List of colors"
                        subheading="Manage the colors offered by the shop">
  <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl ">
    <div class="flex justify-start ">
      <div class="my-4 p-6 w-full">
        <div class="flex items-center gap-4 mb-4">
          <flux:button variant="primary" href="{{ route('colors.create') }}">Create a new Color</flux:button>
        </div>
        <div class="my-4 font-base text-sm text-gray-700 dark:text-gray-300">
            <x-colors.table :colors="$colors"
                            :showView="true"
                            :showEdit="true"
                            :showDelete="true"
            />
        </div>
        <div class="mt-4">
          {{ $colors->links() }}
        </div>
      </div>
    </div>
  </div>
</x-layouts::main-content>