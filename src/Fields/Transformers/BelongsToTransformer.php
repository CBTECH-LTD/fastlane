<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Fields\Transformers;

use CbtechLtd\Fastlane\Contracts\EntryType;
use CbtechLtd\Fastlane\Contracts\Transformer;
use CbtechLtd\Fastlane\Fields\Support\SelectOption;
use CbtechLtd\Fastlane\Fields\Support\SelectOptionCollection;
use CbtechLtd\Fastlane\Fields\Types\BelongsTo;
use CbtechLtd\Fastlane\Fields\Value;
use CbtechLtd\Fastlane\Support\Eloquent\BaseModel;
use Illuminate\Support\Collection;

class BelongsToTransformer implements Transformer
{
    private BelongsTo $field;

    public function __construct(BelongsTo $field)
    {
        $this->field = $field;
    }

    public function get(EntryType $entryType, $value)
    {
        return new Value($entryType, $this->getSelectedOptions($value), $this->field);
    }

    public function set(EntryType $entryType, $value)
    {
        $method = $this->field->getRelationshipMethod();

        if ($id = $value->value()->map(fn(SelectOption $option) => $option->getValue())->first()) {
            $entryType->modelInstance()->{$method}()->associate($id);
        }
    }

    public function fromRequest(EntryType $entryType, $value): Value
    {
        $entry = $this->field->getRelatedEntryType()::query()->key($value)->first();

        return new Value($entryType, $entry->modelInstance(), $this->field);
    }

    /**
     * @param BaseModel $value
     * @return Collection
     */
    protected function getSelectedOptions(BaseModel $value): Collection
    {
        $options = SelectOptionCollection::make([
            SelectOption::make(
                (string)$value->getEntryType()->entryKey(),
                $value->getEntryType()->entryTitle()
            )->select(),
        ]);

        return $options->selected()->values();
    }
}
