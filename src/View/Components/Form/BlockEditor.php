<?php

namespace CbtechLtd\Fastlane\View\Components\Form;

use CbtechLtd\Fastlane\ContentBlocks\ContentBlock;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class BlockEditor extends ReactiveFieldComponent
{
    protected string $view = 'fastlane::components.form.block-editor';

    public function addBlock(string $key, int $position): void
    {
        // FIXME: We are overriding the given position because
        //        there's a bug in the block-editor.js file that
        //       causes it to always pass '0' as the position.
        $position = $this->value->count();

        // TODO: We should check whether the key and position are valid.
        $this->value->splice($position, 0, [
            [ 'block' => $key, 'data' => [] ],
        ]);
    }

    public function removeBlock(string $position): void
    {
        // TODO: We should check whether the key and position are valid.
        if ($this->value->count() < $position) {
            throw new \Exception('Invalid block position');
        }

        $this->value->splice($position, 1);
    }

    protected function viewData(): array
    {
        $blocks = $this->value->map(function (array $b) {
            return $this->field
                ->getBlocks()
                ->getResolved()
                ->get($b['block'])
                ->withValues($b);
        });

        return [
            'blocks' => $blocks->toArray(),
            'availableBlocks' => $this->field->getBlocks(),
        ];
    }

    protected function readValue()
    {
        return Collection::make(Arr::wrap(parent::readValue()));
    }
}
