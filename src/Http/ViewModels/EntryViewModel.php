<?php

namespace CbtechLtd\Fastlane\Http\ViewModels;

use CbtechLtd\Fastlane\Fields\Types\FieldCollection;
use CbtechLtd\Fastlane\Http\ViewModels\Traits\WithLinks;
use CbtechLtd\Fastlane\Http\ViewModels\Traits\WithMeta;
use CbtechLtd\Fastlane\Models\Entry;
use CbtechLtd\Fastlane\Support\Eloquent\BaseModel;
use Illuminate\Support\Collection;
use Spatie\ViewModels\ViewModel;

class EntryViewModel extends ViewModel
{
    use WithMeta, WithLinks;

    private string $entryType;
    private FieldCollection $fields;
    private Entry $model;

    /** @var string[] */
    protected $ignore = ['withMeta', 'withLinks'];

    /**
     * EntryViewModel constructor.
     *
     * @param string          $entryType
     * @param FieldCollection $fields
     * @param Entry           $model
     */
    public function __construct(string $entryType, FieldCollection $fields, Entry $model)
    {
        $this->entryType = $entryType;
        $this->fields = $fields;
        $this->model = $model;
    }

    public function model(): Entry
    {
        return $this->model;
    }

    /**
     * Provide the model ID for routes.
     *
     * @return mixed
     */
    public function id()
    {
        return ($this->model->exists)
            ? $this->entryType::entryRouteKey($this->model)
            : '';
    }

    /**
     * Provide the model title.
     *
     * @return string
     */
    public function title(): string
    {
        return $this->entryType::entryTitle($this->model);
    }

    /**
     * Provide the entry type.
     *
     * @return string
     */
    public function entryType(): string
    {
        return $this->entryType;
    }

    /**
     * Provide the model data.
     *
     * @return array|BaseModel
     */
    public function data(): array
    {
        return $this->fields->getData($this->model, $this->entryType);
    }

    public function fields(): FieldCollection
    {
        return $this->fields;
    }

    public function toArray(): array
    {
        return $this->items()->all();
    }

    /**
     * @inheritDoc
     */
    protected function buildSchemaForMeta()
    {
        return $this->fields;
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultLinks(): Collection
    {
        $links = Collection::make([]);

        // Add general links...
        $links
            ->when($this->entryType::routes()->has('index'), function (Collection $c) {
                return $c->put('top', $this->entryType::routes()->get('index')->url());
            })
            ->when($this->entryType::routes()->has('create'), function (Collection $c) {
                return $c->put('create', $this->entryType::routes()->get('create')->url());
            });

        // Add links to an existing model..
        if ($this->model->exists) {
            $links
                ->when($this->entryType::routes()->has('edit'), function (Collection $c) {
                    return $c->put('self', $this->entryType::routes()->get('edit')->url($this->model));
                })
                ->when($this->entryType::routes()->has('delete'), function (Collection $c) {
                    return $c->put('self.delete', $this->entryType::routes()->get('delete')->url($this->model));
                });

            return $links;
        }

        // Otherwise add links related to a new model...
        $links->when($this->entryType::routes()->has('store'), function (Collection $c) {
            return $c->put('self', $this->entryType::routes()->get('store')->url());
        });

        return $links;
    }
}
