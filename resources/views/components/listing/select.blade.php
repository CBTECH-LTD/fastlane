<span>
    @if ($multiple)
        @foreach ($value as $v)
            {{ $v->getLabel() }} <br>
        @endforeach
    @else
        {{ $value[0]->getLabel() }}
    @endif
</span>
