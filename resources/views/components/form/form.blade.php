<form id="{{ $formId }}" action="{{ $action }}" method="POST">
    @csrf
    @if ($method === 'update') @method('PATCH') @endif

    <x-fl-boxed-card>
        @foreach ($fields as $field)
            <x-dynamic-component :component="$field->formComponent()::tag()" :field="$field" :model="$model" :entry-type="$entryType"></x-dynamic-component>
            @if (!$loop->last && $field instanceof \CbtechLtd\Fastlane\Fields\Types\Panel)
                <hr class="border-t border-dashed border-gray-500 my-8"/>
            @endif
        @endforeach
    </x-fl-boxed-card>
</form>
