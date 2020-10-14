<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\View\Components\Listing;

use CbtechLtd\Fastlane\Support\Eloquent\BaseModel;
use CbtechLtd\Fastlane\View\Components\ReactiveComponent;
use Illuminate\Database\Eloquent\Model;

class ReactiveToggle extends ReactiveComponent
{
    public BaseModel $model;
    public string $attribute;

    public function mount(Model $model, string $attribute, $value)
    {
        $this->model = $model;
        $this->attribute = $attribute;
    }

    public function getValueProperty()
    {
        return $this->model->{$this->attribute}->value();
    }

    public function toggleOn()
    {
        $this->model->getEntryType()->update([
            $this->attribute => true,
        ]);
    }

    public function toggleOff()
    {
        $this->model->getEntryType()->update([
            $this->attribute => false,
        ]);
    }

    public function render()
    {
        return view('fastlane::components.listing.reactive-toggle');
    }
}
