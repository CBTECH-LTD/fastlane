<?php

namespace CbtechLtd\Fastlane\FileAttachment;

use CbtechLtd\Fastlane\Http\Requests\EntryAttachmentStoreRequest;
use CbtechLtd\Fastlane\Support\Contracts\SchemaField;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class StoreDraftAttachment
{
    protected SchemaField $field;

    public function __construct(SchemaField $field)
    {
        $this->field = $field;
    }

    public function __invoke(EntryAttachmentStoreRequest $request)
    {
        $disk = Config::get('fastlane.attachment_disk');

        $model = DraftAttachment::create([
            'draft_id' => $request->input('draft_id'),
            'file'     => $request->file('file')->store($this->field->getStorageDirectory(), $disk),
        ]);

        return Storage::disk($disk)->url($model->file);
    }


}
