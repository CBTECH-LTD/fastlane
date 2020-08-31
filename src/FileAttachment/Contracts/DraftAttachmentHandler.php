<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\FileAttachment\Contracts;

use CbtechLtd\Fastlane\FileAttachment\AttachmentValue;
use CbtechLtd\Fastlane\Http\Requests\EntryAttachmentStoreRequest;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\HasAttachments;
use Illuminate\Support\Collection;

interface DraftAttachmentHandler
{
    /**
     * @param string     $draftId
     * @param array|null $files
     * @return Collection<AttachmentValue>
     */
    public function findDrafts(string $draftId, ?array $files = null): Collection;

    /**
     * @param EntryAttachmentStoreRequest $request
     * @param HasAttachments              $field
     * @return AttachmentValue
     */
    public function write(EntryAttachmentStoreRequest $request, HasAttachments $field): AttachmentValue;
}
