<?php

namespace CbtechLtd\Fastlane\Http\Middleware;

use CbtechLtd\Fastlane\Fastlane;
use CbtechLtd\Fastlane\Http\Requests\FastlaneRequest;
use CbtechLtd\Fastlane\Support\Contracts\EntryType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ResolveFastlaneRequest
{
    private Fastlane $fastlane;

    public function __construct()
    {
        $this->fastlane = app('fastlane');
    }

    public function handle($request, \Closure $next, $routeGroup)
    {
        $entryType = $this->getEntryType($request, $routeGroup);
        $entryInstance = $this->getEntryInstance($request, $entryType);

        $newRequest = tap(FastlaneRequest::createFrom($request), function (FastlaneRequest $req) use ($entryType, $entryInstance) {
            $req->setEntryType($entryType)->setEntryInstance($entryInstance);
        });

        $this->fastlane->setRequest($newRequest);

        return $next($newRequest);
    }

    protected function getEntryType(Request $request, $routeGroup): EntryType
    {
        $groupUrl = config("fastlane.${routeGroup}.url_prefix") . '/';
        $urlPrefix = Str::replaceLast('/', '/entry-types/', $groupUrl);

        return $this->fastlane->getEntryTypeByIdentifier(
            explode('/', Str::replaceFirst($urlPrefix, '', '/' . $request->path()))[0]
        );
    }

    protected function getEntryInstance(Request $request, EntryType $entryType): ?Model
    {
        return ! is_null($request->id)
            ? $entryType->findItem($request->id)
            : null;
    }
}
