<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\View\Components;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

class Paginator extends Component implements Arrayable
{
    private LengthAwarePaginator $paginator;

    public function __construct(LengthAwarePaginator $paginator)
    {
        $this->paginator = $paginator;
    }


    public function render()
    {
        return view('fastlane::components.paginator', $this->toArray());
    }

    public function toArray()
    {
        $pages = Collection::make($this->paginator->getUrlRange($this->paginator->currentPage() - 3, $this->paginator->currentPage() + 3))
            ->filter(fn($v, $k) => $k > 0 && $k <= $this->paginator->lastPage())
            ->map(fn($v, $k) => (object)[
                'number'    => $k,
                'url'       => $v,
                'isCurrent' => $k === $this->paginator->currentPage(),
            ])
            ->values();

        return [
            'currentPage'     => $this->paginator->currentPage(),
            'lastPage'        => $this->paginator->lastPage(),
            'total'           => $this->paginator->total(),
            'pageUrls'        => $pages,
            'firstPageUrl'    => $this->paginator->path(),
            'previousPageUrl' => $this->paginator->previousPageUrl(),
            'nextPageUrl'     => $this->paginator->nextPageUrl(),
            'lastPageUrl'     => $this->paginator->url($this->paginator->lastPage()),
            'hasMoreBefore'   => $this->hasMoreBefore($pages),
            'hasMoreAfter'    => $this->hasMoreAfter($pages),
        ];
    }

    protected function hasMoreBefore(Collection $pages): bool
    {
        return $pages->isNotEmpty()
            ? $pages->first()->number > 1
            : false;
    }

    protected function hasMoreAfter(Collection $pages): bool
    {
        return $pages->isNotEmpty()
            ? $pages->last()->number < $this->paginator->lastPage()
            : false;
    }
}
