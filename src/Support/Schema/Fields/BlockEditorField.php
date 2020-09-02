<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

use CbtechLtd\Fastlane\Support\Contracts\EntryInstance as EntryInstanceContract;
use CbtechLtd\Fastlane\Support\Schema\Fields\Concerns\HandlesAttachments;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\HasAttachments;
use Illuminate\Support\Facades\URL;

class BlockEditorField extends AbstractBaseField implements HasAttachments
{
    use HandlesAttachments;

    protected $default = [];

    public function getType(): string
    {
        return 'blockEditor';
    }

    protected function getTypeRules(): array
    {
        return [
            $this->getName()                    => 'array',
            $this->getName() . '.blocks'        => 'sometimes|array',
            $this->getName() . '.blocks.*'      => "sometimes|array",
            $this->getName() . '.blocks.*.type' => "required|string",
            $this->getName() . '.blocks.*.data' => "required|array",
            $this->getName() . '__draft_id'     => "required_with:{$this->getName()}|uuid",
        ];
    }

    protected function resolveConfig(EntryInstanceContract $entryInstance, string $destination): void
    {
        $this->resolvedConfig = $this->resolvedConfig->merge([
            'csrfToken' => csrf_token(),
            'links'     => [
                'attachments' => URL::relative("cp.{$entryInstance->type()->identifier()}.attachments", $this->getName()),
            ],
        ]);
    }

    public function getAcceptableMimetypes(): array
    {
        return [];
    }

    protected function getMigrationMethod(): array
    {
        return ['json'];
    }

    public function toModelAttribute(): array
    {
        return [
            $this->getName() => 'array',
        ];
    }
}
