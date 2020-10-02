<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Fields;

use CbtechLtd\Fastlane\EntryTypes\FileManager\File as FileModel;
use Illuminate\Support\Collection;

class File extends Field implements Contracts\HasAttachments
{
    use Traits\HasAttachments;

    protected string $component = 'file';
    protected array $accept = [];
    protected $value = [];

    public function get()
    {
        return $this->value;
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
        return $this->accept;
    }

    public function accept(array $accept): self
    {
        $this->accept = $accept;
        return $this;
    }

    public function multiple(bool $multiple = true): self
    {
        return $this->setConfig('multiple', $multiple);
    }

    public function isMultiple(): bool
    {
        return $this->getConfig('multiple', false);
    }

    public function getDefault()
    {
        return $this->getConfig('default', []);
    }

    protected function resolveConfig(): void
    {
        $this
            ->setConfig('accept', $this->getAcceptableMimetypes())
            ->setConfig('links', [
                'fileManager' => route('cp.file-manager.index'),
            ]);
    }
}
