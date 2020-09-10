<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

use CbtechLtd\Fastlane\Support\Contracts\EntryInstance;
use CbtechLtd\Fastlane\Support\Schema\Fields\Concerns\ExportsToApiAttribute;
use CbtechLtd\Fastlane\Support\Schema\Fields\Concerns\HandlesAttachments;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\ExportsToApiAttribute as ExportsToApiAttributeContract;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\HasAttachments;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\URL;

class RichEditorField extends AbstractBaseField implements ExportsToApiAttributeContract, HasAttachments
{
    use HandlesAttachments, ExportsToApiAttribute;

    public function getType(): string
    {
        return 'richEditor';
    }

    public function getAcceptableMimetypes(): array
    {
        return [];
    }

    public function toApiAttribute(Model $model, array $options = [])
    {
        if ($this->toApiAttributeCallback) {
            return call_user_func($this->toApiAttributeCallback, $model);
        }

        if (Arr::get($options, 'output', 'collection') === 'collection') {
            return [];
        }

        return Collection::make($this->readValue($model))->mapWithKeys(
            fn($value, $key) => [
                $key => Arr::get($options, 'output', 'collection') === 'collection'
                    ? null
                    : $value,
            ]
        )->all();
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
