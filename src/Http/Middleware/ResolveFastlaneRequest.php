<?php

namespace CbtechLtd\Fastlane\Http\Middleware;

use CbtechLtd\Fastlane\Fastlane;
use CbtechLtd\Fastlane\Http\Requests\FastlaneRequest;
use CbtechLtd\Fastlane\Support\Contracts\EntryInstance;
use CbtechLtd\Fastlane\Support\Contracts\EntryType;
use Illuminate\Http\Request;

class ResolveFastlaneRequest
{
    private Fastlane $fastlane;

    public function __construct()
    {
        $this->fastlane = app('fastlane');
    }

    public function handle($request, \Closure $next, string $entryTypeIdentifier)
    {
        $entryType = $this->getEntryType($entryTypeIdentifier);
        $entryInstance = $this->getEntryInstance($request, $entryType);

        $newRequest = tap(FastlaneRequest::createFrom($request), function (FastlaneRequest $req) use ($entryType, $entryInstance) {
            $req->setEntryType($entryType);
            $req->setEntryInstance($entryInstance);
        });

        $this->fastlane->setRequest($newRequest);

        return $next($newRequest);
    }

    protected function getEntryType($identifier): EntryType
    {
        return $this->fastlane->getEntryTypeByIdentifier($identifier);
    }

    protected function getEntryInstance(Request $request, EntryType $entryType): ?EntryInstance
    {
        return ! is_null($request->id)
            ? $entryType->findItem($request->id)
            : $entryType->newInstance(null);
    }
}
