<?php

namespace CbtechLtd\Fastlane\View\Components\Form;

class BlockEditor extends ReactiveFieldComponent
{
    protected string $view = 'fastlane::components.form.block-editor';

    public function addBlock(string $key, int $position): void
    {
        $block = $this->field->getBlocks()->getResolved()->get($key);

        // TODO: We should check whether the key and position are valid.
        $this->value->splice($position, 0, [$block]);
    }

    protected function viewData(): array
    {
        return [
            'blocks' => [],
            'availableBlocks' => $this->field->getBlocks(),
        ];
    }
}
