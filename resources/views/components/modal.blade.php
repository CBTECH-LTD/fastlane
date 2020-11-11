<div x-data="fl.Modal({{ $show }})"
     x-init="init()"
     x-show="show"
     x-on:keydown.escape.window="close()"
     x-cloak>
    <div>
        <div x-show="show" x-cloak :class="classes" class="z-40 fixed top-0 left-0 bottom-0 right-0 bg-black transition-opacity duration-200 ease-in-out" tabindex="-1" role="dialog">
            <!-- BACKDROP -->
        </div>
        <div x-show="show" x-cloak @click.away="close()">
            <!-- MODAL CARD -->
            <div>
                TEST
            </div>
        </div>
    </div>
</div>
