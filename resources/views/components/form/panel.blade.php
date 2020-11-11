<div>
    <h2 class="flex items-start justify-start font-light text-gray-900 text-xl mb-6">
        @if ($field->getIcon())
            <span class="pr-4">
                <x-fl-icon :name="$field->getIcon()"></x-fl-icon>
            </span>
        @endif
        {{ $field->getLabel() }}
    </h2>

    @foreach ($field->getFields() as $child)
        <x-dynamic-component :component="$child->formComponent()::tag()" :field="$child" :model="$model" :entry-type="$entryType"></x-dynamic-component>
    @endforeach
</div>
