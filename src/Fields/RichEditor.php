<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Fields;

use Illuminate\Support\Facades\URL;

class RichEditor extends Field implements Contracts\HasAttachments
{
    use Traits\HasAttachments;

    protected string $component = 'richEditor';

    protected function resolveConfig(): void
    {
        $this->config
            ->put('csrfToken', csrf_token())
            ->put('links', [
                'fileManager' => URL::relative('cp.file-manager.index'),
            ]);
    }
}
