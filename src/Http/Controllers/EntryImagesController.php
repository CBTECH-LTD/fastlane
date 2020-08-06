<?php

namespace CbtechLtd\Fastlane\Http\Controllers;

use CbtechLtd\Fastlane\Http\Requests\EntryImageStoreRequest;

class EntryImagesController extends Controller
{
    public function store(EntryImageStoreRequest $request)
    {
        $field = $request->field();

        return response()->json([
            'url' => $field->processUpload($request),
        ]);
    }
}
