<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Fields\Types;

use CbtechLtd\Fastlane\EntryTypes\FileManager\FileManagerEntryType;
use CbtechLtd\Fastlane\Fields\Field;

class RichEditor extends Field implements Contracts\HasAttachments
{
    use Traits\HasAttachments;

    protected string $component = 'richEditor';

    public function __construct(string $label, ?string $attribute = null)
    {
        parent::__construct($label, $attribute);

        $this->mergeConfig([
            'csrfToken'   => csrf_token(),
            'acceptFiles' => true,
            'links'       => [
                'fileManager' => FileManagerEntryType::routes()->get('index')->url(),
            ],
        ]);
    }

    /**
     * Disable files in the content editor.
     *
     * @return $this
     */
    public function disableFiles(): self
    {
        return $this->setConfig('acceptFiles', false);
    }
}
