<div class="relative">
    @foreach ($items as $item)
        <div>
            {!! $item->render() !!}
        </div>
    @endforeach
</div>
