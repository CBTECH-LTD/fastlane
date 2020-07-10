<?php

namespace CbtechLtd\Fastlane\Http\Controllers;

use CbtechLtd\Fastlane\Http\Requests\EntryAttachmentStoreRequest;
use CbtechLtd\Fastlane\Support\Schema\FieldTypes\Concerns\HandlesAttachments;

class EntryAttachmentsController extends Controller
{
    public function store(EntryAttachmentStoreRequest $request)
    {
        /** @var HandlesAttachments $field */
        $field = $request->field();

        return response()->json([
            'url' => call_user_func($field->getAddFileCallback(), $request),
        ]);
    }
}
