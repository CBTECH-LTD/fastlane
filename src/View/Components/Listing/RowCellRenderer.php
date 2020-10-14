<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\View\Components\Listing;

use CbtechLtd\Fastlane\Fields\Field;
use CbtechLtd\Fastlane\View\Components\Component;
use CbtechLtd\Fastlane\View\Components\ReactiveComponent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;

class RowCellRenderer extends Component
{
    public Model $model;
    public Field $field;

    public function __construct(Model $model, Field $field)
    {
        $this->field = $field;
        $this->model = $model;
    }

    public function renderedComponent()
    {
        $component = $this->field->listingComponent();
        $instance = new $component($this->model, $this->field->getAttribute(), $this->getValue());

        $props = ' :model="$model" attribute="' . $this->field->getAttribute() . '" :value="$getValue()"';

        return $instance instanceof ReactiveComponent
            ? '<livewire:' . $component::tag() . $props . '/>'
            : '<x-' . $component::tag() . $props . '/>';
    }

    public function render()
    {
        return Blade::compileString($this->renderedComponent());
    }

    public function getValue()
    {
        return optional($this->model->{$this->field->getAttribute()})->value();
    }
}
