<div class="w-full flex justify-center items-center my-2">
    @if ($value->value() === true)
        <span class="text-green-500 uppercase font-semibold">
            ON
        </span>
    @else
        <span class="text-black uppercase font-semibold">
            OFF
        </span>
    @endif
</div>
