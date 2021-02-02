<?php

namespace CbtechLtd\Fastlane\EntryTypes;

use CbtechLtd\Fastlane\Contracts\EntryType as EntryTypeContract;
use CbtechLtd\Fastlane\Fastlane;
use CbtechLtd\Fastlane\Fields\Field;
use CbtechLtd\Fastlane\Fields\Types\FieldCollection;
use CbtechLtd\Fastlane\Models\Entry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Webmozart\Assert\Assert;

class EntryInstance extends Model
{
    protected Model $cachedModel;

    protected $fillable = [
        'entry_type',
        'entry_id',
        'for',
    ];

    protected $attributes = [
        'for' => 'onListing',
    ];

    protected $guarded = [];

    public static function newFromModel(string $entryType, Model $model): EntryInstance
    {
        return tap(new static([
            'entry_type' => $entryType,
            'entry_id' => $model->getRouteKey(),
        ]), function (EntryInstance $inst) use ($model) {
            $inst->cachedModel = $model;
        });
    }

    public function for(string $for): self
    {
        Assert::oneOf($for, ['onListing', 'onCreate', 'onUpdate']);

        $this->attributes['for'] = $for;

        return $this;
    }

    public function forListing(): self
    {
        return $this->for('onListing');
    }

    public function forCreate(): self
    {
        return $this->for('onCreate');
    }

    public function forUpdate(): self
    {
        return $this->for('onUpdate');
    }

    public function id()
    {
        return $this->getAttribute('entry_id');
    }

    public function getIdAttribute(): string
    {
        return $this->id();
    }

    public function title(): string
    {
        return $this->type()::entryTitle($this->model());
    }

    /**
     * @return string | EntryTypeContract
     */
    public function type(): string
    {
        return $this->getAttribute('entry_type');
    }

    public function model(): Entry
    {
        if (!isset($this->cachedModel)) {
            $this->cachedModel = (empty($this->id()))
                ? $this->type()::repository()->newModel()
                : $this->type()::repository()->findOrFail($this->id())->model();
        }

        return $this->cachedModel;
    }

    public function links(): Collection
    {
        return once(function () {
            return Collection::make([
                'top' => Fastlane::cpRoute('entry-type.index', $this->type()::key()),
                'create' => Fastlane::cpRoute('entry-type.create', $this->type()::key()),
                'self' => Fastlane::cpRoute('entry-type.edit', [$this->type()::key(), $this->id()]),
            ]);
        });
    }

    public function fields(): FieldCollection
    {
        return once(function () {
            $fields = new FieldCollection($this->type()::fields());

            return $fields->{$this->attributes['for']}();
        });
    }

    public function exists(): bool
    {
        return $this->model()->exists;
    }

    public function data(): array
    {
        return $this->processData();
    }

    public function get(string $key)
    {
        return Arr::get($this->processData(), $key);
    }

    public function commit(array $options = [])
    {
        if ($this->model()->exists) {
            $this->model = $this->type()::repository()->update($this->model(), $options);

            return $this;
        }

        $this->model = $this->type()::repository()->store($options);

        return $this;
    }

    protected function processData(): array
    {
        return once(function () {
            return $this->fields()->flattenFields()->getCollection()
                ->mapWithKeys(function (Field $field) {
                    return [
                        $field->getAttribute() => $field->read($this->model(), $this->type()),
                    ];
                })->all();
        });
    }
}
