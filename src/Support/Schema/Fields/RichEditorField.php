<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

use CbtechLtd\Fastlane\Support\Contracts\EntryInstance;
use CbtechLtd\Fastlane\Support\Schema\Fields\Concerns\HandlesAttachments;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\HasAttachments;
use Illuminate\Support\Facades\URL;

class RichEditorField extends AbstractBaseField implements HasAttachments
{
    use HandlesAttachments;

    public function getType(): string
    {
        return 'richEditor';
    }

    public function getAcceptableMimetypes(): array
    {
        return [];
    }

    protected function resolveConfig(EntryInstance $entryInstance, string $destination): void
    {
        $this->resolvedConfig = $this->resolvedConfig->merge([
            'acceptFiles' => $this->acceptFiles,
            'csrfToken'   => csrf_token(),
            'links'       => [
                'fileManager' => URL::relative("cp.file-manager.index"),
            ],
        ]);
    }

    protected function getMigrationMethod(): array
    {
        return ['longText'];
    }
}
