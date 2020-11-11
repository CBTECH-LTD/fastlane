<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\View\Components\Listing;

use CbtechLtd\Fastlane\View\Components\Component;
use Illuminate\Database\Eloquent\Model;

class Chip extends Component
{
    public Model $model;
    public string $attribute;
    public $value;

    public function __construct(Model $model, string $attribute, $value)
    {
        $this->model = $model;
        $this->value = $value;
        $this->attribute = $attribute;
    }

    public function render()
    {
        return view('fastlane::components.listing.chip', [
            'value' => $this->value,
        ]);
    }
}
