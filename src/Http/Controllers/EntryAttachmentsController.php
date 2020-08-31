<?php

namespace CbtechLtd\Fastlane\Http\Controllers;

use CbtechLtd\Fastlane\Http\Requests\EntryAttachmentStoreRequest;

class EntryAttachmentsController extends Controller
{
    public function store(EntryAttachmentStoreRequest $request)
    {
        return response()->json(
            $request->field()->storeAttachment($request)->toArray()
        );
    }

    public function read()
    {
        abort(503);
    }
}
