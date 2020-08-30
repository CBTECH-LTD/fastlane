<?php

namespace CbtechLtd\Fastlane\FileAttachment;

use CbtechLtd\Fastlane\FileAttachment\Contracts\DraftAttachmentHandler;
use CbtechLtd\Fastlane\Http\Requests\EntryAttachmentStoreRequest;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\HasAttachments;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;

class StoreDraftAttachment implements DraftAttachmentHandler
{
    protected HasAttachments $field;

    public function __construct(HasAttachments $field)
    {
        $this->field = $field;
    }

    public function handle(EntryAttachmentStoreRequest $request): DraftAttachment
    {
        $disk = Config::get('fastlane.attachment_disk');
        $data = $request->validated();

        /** @var UploadedFile $file */
        $file = $data['files'][0];

        return DraftAttachment::create([
            'draft_id' => $data['draft_id'],
            'name'     => $data['name'],
            'file'     => $file->store($this->field->getStorageDirectory(), $disk),
        ]);
    }
}
