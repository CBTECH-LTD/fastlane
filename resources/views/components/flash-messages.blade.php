<div>
    @if(!empty($messages))
        <div class="mt-8 mb-4 px-12">
            @foreach ($messages as $msg)
                <div class="flash-message flash-message--type-{{ $msg->type }}">
                    @isset($msg->icon)
                        <div class="flash-message__icon">
                            <x-fl-icon name="{{ $msg->icon }}"></x-fl-icon>
                        </div>
                    @endisset
                    <div class="flash-message__content">
                        {{ $msg->message }}
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>
