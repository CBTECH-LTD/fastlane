<div class="pb-4">
    <div class="relative w-full py-3 flex items-center text-gray-600 opacity-100 menu-group__label">
        <span class="flex-shrink bg-white border border-gray-400 border-dashed rounded text-sm font-semibold uppercase tracking-wider z-10 ml-12 px-4">{{ $label }}</span>
    </div>
    <x-fl-menu-wrapper class="transition-all duration-300" :items="$children"></x-fl-menu-wrapper>
</div>
