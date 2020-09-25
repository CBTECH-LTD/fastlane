<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Fields\Types;

use CbtechLtd\Fastlane\Contracts\Transformer;
use CbtechLtd\Fastlane\EntryTypes\FileManager\File as FileModel;
use CbtechLtd\Fastlane\Fields\Field;
use CbtechLtd\Fastlane\Fields\Transformers\FileTransformer;
use Illuminate\Support\Collection;

class File extends Field implements Contracts\HasAttachments
{
    use Traits\HasAttachments;

    protected string $component = 'file';
    protected array $accept = [];

    public function __construct(string $label, ?string $attribute = null)
    {
        parent::__construct($label, $attribute);

        $this->mergeConfig([
            'default'  => [],
            'accept'   => [],
            'multiple' => false,
            'links'    => [
//                'fileManager' => route('fastlane.cp.file-manager.index'),
            ],
        ]);
    }

    public function transformer(): Transformer
    {
        return new FileTransformer($this);
    }

    public function set($value): Field
    {
        $items = FileModel::query()
            ->whereKey($value)
            ->get()
            ->toArray();

        $this->value = Collection::make($items);
        return $this;
    }

    public function getAcceptableMimetypes(): array
    {
        return $this->getConfig('accept');
    }

    public function accept(array $accept): self
    {
        return $this->setConfig('accept', $accept);
    }

    public function multiple(bool $multiple = true): self
    {
        return $this->setConfig('multiple', $multiple);
    }

    public function isMultiple(): bool
    {
        return $this->getConfig('multiple');
    }
}
