<?php

namespace CbtechLtd\Fastlane\Http\Controllers;

use CbtechLtd\Fastlane\FileAttachment\DraftAttachment;
use CbtechLtd\Fastlane\Http\Requests\EntryAttachmentStoreRequest;

class EntryAttachmentsController extends Controller
{
    public function store(EntryAttachmentStoreRequest $request)
    {
        /** @var DraftAttachment */
        $model = $request->field()->storeAttachment($request);

        return response()->json([
            'file' => $model->file,
            'name' => $model->name,
            'url'  => $model->getUrlAttribute(),
        ]);
    }
}
