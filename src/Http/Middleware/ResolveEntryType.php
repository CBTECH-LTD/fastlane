<?php

namespace CbtechLtd\Fastlane\Http\Middleware;

use CbtechLtd\Fastlane\Fastlane;
use CbtechLtd\Fastlane\Support\Contracts\EntryType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ResolveEntryType
{
    private Fastlane $fastlane;

    public function __construct()
    {
        $this->fastlane = app('fastlane');
    }

    public function handle($request, \Closure $next, $routeGroup)
    {
        $this->fastlane->setRequest($request);
        $this->resolveEntryType($request, $routeGroup);

        return $next($request);
    }

    protected function resolveEntryType(Request $request, $routeGroup): void
    {
        $groupUrl = config("fastlane.${routeGroup}.url_prefix") . '/';
        $urlPrefix = Str::replaceFirst('/', '', $groupUrl);

        $entryType = $this->fastlane->getEntryTypeByIdentifier(
            explode('/', Str::replaceFirst($urlPrefix, '', $request->path()))[0]
        );

        $this->fastlane->setRequestEntryType($entryType->resolve());
        $this->resolveEntryInstance($request, $entryType);
    }

    protected function resolveEntryInstance(Request $request, EntryType $entryType): void
    {
        if (! is_null($request->id)) {
            $this->fastlane->setRequestEntry($entryType->findItem($request->id));
        }
    }
}
