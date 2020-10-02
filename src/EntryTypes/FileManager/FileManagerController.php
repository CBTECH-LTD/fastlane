<?php

namespace CbtechLtd\Fastlane\EntryTypes\FileManager;

use CbtechLtd\Fastlane\FastlaneFacade;
use CbtechLtd\Fastlane\Http\Controllers\EntriesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class FileManagerController extends EntriesController
{
    public function create()
    {
        return redirect()->route("cp.{$this->entryType()->identifier()}.index");
    }

    public function store(Request $request)
    {
        $this->entryType()->storeMany($request);

        FastlaneFacade::flashSuccess($this->entryType()->name() . ' created successfully.', 'thumbs-up');

        return Redirect::route("cp.{$this->entryType()->identifier()}.index");
    }
}
