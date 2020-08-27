<?php

namespace CbtechLtd\Fastlane\FileAttachment;

use CbtechLtd\Fastlane\Http\Requests\EntryAttachmentStoreRequest;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\HasAttachments;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;

class StoreDraftAttachment
{
    protected HasAttachments $field;

    public function __construct(HasAttachments $field)
    {
        $this->field = $field;
    }

    public function __invoke(EntryAttachmentStoreRequest $request): string
    {
        $disk = Config::get('fastlane.attachment_disk');
        $data = $request->validated();

        /** @var UploadedFile $file */
        $file = $data['files'][0];

        $model = DraftAttachment::create([
            'draft_id' => $data['draft_id'],
            'name'     => $data['name'],
            'file'     => $file->store($this->field->getStorageDirectory(), $disk),
        ]);

//        return Storage::disk($disk)->url($model->file);
        return $model->file;
    }
}
