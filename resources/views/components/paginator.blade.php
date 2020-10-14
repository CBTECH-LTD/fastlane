<div {{ $attributes->merge(['class' => 'flex items-center justify-center font-bold']) }}>
    @if ($currentPage > 1)
        <x-fl-button :href="$firstPageUrl" color="brand" variant="outline" size="sm" class="mx-1">
            <x-fl-icon name="step-backward"></x-fl-icon>
        </x-fl-button>
    @endif
    @if ($previousPageUrl)
        <x-fl-button :href="$previousPageUrl" color="brand" variant="outline" size="sm" class="mx-1">
            <x-fl-icon name="angle-left"></x-fl-icon>
        </x-fl-button>
    @endif
    @if ($hasMoreBefore)
        <span class="mx-1 px-1 text-gray-700"><x-fl-icon name="ellipsis-h"></x-fl-icon></span>
    @endif
    @foreach ($pageUrls as $page)
        <x-fl-button :href="$page->url" color="brand" variant="{{ $page->isCurrent ? 'solid' : 'outline'}}" size="sm" class="mx-1">{{ $page->number }}</x-fl-button>
    @endforeach
    @if ($hasMoreAfter)
        <span class="mx-1 px-1 text-gray-700"><x-fl-icon name="ellipsis-h"></x-fl-icon></span>
    @endif
    @if ($nextPageUrl)
        <x-fl-button :href="$nextPageUrl" color="brand" variant="outline" size="sm" class="mx-1">
            <x-fl-icon name="angle-right"></x-fl-icon>
        </x-fl-button>
    @endif
    @if ($currentPage < $lastPage)
        <x-fl-button :href="$lastPageUrl" color="brand" variant="outline" size="sm" class="mx-1">
            <x-fl-icon name="step-forward"></x-fl-icon>
        </x-fl-button>
    @endif
</div>
