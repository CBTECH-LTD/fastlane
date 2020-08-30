<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\FileAttachment\Contracts;

use CbtechLtd\Fastlane\FileAttachment\DraftAttachment;
use CbtechLtd\Fastlane\Http\Requests\EntryAttachmentStoreRequest;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\HasAttachments;

interface DraftAttachmentHandler
{
    public function __construct(HasAttachments $field);

    public function handle(EntryAttachmentStoreRequest $request): DraftAttachment;
}
