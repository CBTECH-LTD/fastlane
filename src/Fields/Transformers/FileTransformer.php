<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Fields\Transformers;

use CbtechLtd\Fastlane\Contracts\EntryType;
use CbtechLtd\Fastlane\Contracts\Transformer;
use CbtechLtd\Fastlane\EntryTypes\FileManager\File as FileModel;
use CbtechLtd\Fastlane\Fields\Types\File;
use CbtechLtd\Fastlane\Fields\Value;
use Illuminate\Support\Collection;

class FileTransformer implements Transformer
{
    private File $field;

    public function __construct(File $field)
    {
        $this->field = $field;
    }

    public function get(EntryType $entryType, $value)
    {
        $items = FileModel::query()
            ->whereKey(is_array($value) ? $value : json_decode($value))
            ->get()
            ->toArray();

        return Collection::make($items);
    }

    public function set(EntryType $entryType, $value)
    {
        return $value->value()->pluck('id')->toJson();
    }

    public function fromRequest(EntryType $entryType, $value): Value
    {
        $items = FileModel::query()
            ->whereKey(is_array($value) ? $value : json_decode($value))
            ->get();

        return new Value($entryType, Collection::make($items->toArray()));
    }
}
