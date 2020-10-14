<div class="mt-2 mb-4">
    <div class="relative block flex flex-wrap @if ($stacked) flex-col @else flex-row @endif">
        @if ($required)
            <span class="absolute left-0 top-0 -ml-4 text-red-600">*</span>
        @endif
        @isset($label)
            <label class="block relative font-medium text-base text-gray-600 mb-4 @if ($stacked) md:w-2/5 lg:w-2/6 @else w-full @endif @if ($required) label--required @endif">
                <span @if (!empty($errors)) class="text-red-600" @endif>
                    {{ $label ?? '' }}
                </span>
                <span class="block relative text-sm text-gray-500 font-normal mt-2 pr-4">
                    {{ $description ?? '' }}
                </span>
            </label>
        @endisset
        <div class="w-full rounded @if (! $stacked) md:w-3/5 lg:w-4/6 @endif">
            <div class="w-full">
                {{ $slot }}
            </div>
            <div class="w-full mt-2">
                @isset($errors)
                    <span class="block my-1 text-sm text-red-600">
                        @foreach ($errors as $e)
                            <span class="block">{{ $e }}</span>
                        @endforeach
                    </span>
                @endisset
            </div>
        </div>
    </div>
</div>
