<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Fields\Types;

use CbtechLtd\Fastlane\Fields\Field;
use Illuminate\Support\Facades\URL;

class RichEditor extends Field implements Contracts\HasAttachments
{
    use Traits\HasAttachments;

    protected string $component = 'richEditor';

    public function __construct(string $label, ?string $attribute = null)
    {
        parent::__construct($label, $attribute);

        $this->mergeConfig([
            'csrfToken' => csrf_token(),
            'links'     => [
//                'fileManager' => URL::relative('fastlane.cp.file-manager.index'),
            ],
        ]);
    }
}
