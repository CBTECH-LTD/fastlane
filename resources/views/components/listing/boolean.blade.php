<div class="w-full flex items-center my-2">
    @if ($value === true)
        <span class="px-2 rounded-sm border border-green-200 bg-green-100 text-green-500 uppercase font-semibold">
            ON
        </span>
    @else
        <span class="px-2 rounded-sm border border-gray-300 bg-gray-200 text-graay-700 uppercase font-semibold">
            OFF
        </span>
    @endif
</div>
