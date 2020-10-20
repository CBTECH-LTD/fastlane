<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Http\Controllers;

use CbtechLtd\Fastlane\Contracts\EntryType;
use CbtechLtd\Fastlane\EntryTypes\BackendUser\BackendUserEntryType;
use CbtechLtd\Fastlane\Fields\Field;
use CbtechLtd\Fastlane\Http\Transformers\EntryTypeResourceCollection;
use CbtechLtd\Fastlane\Support\Eloquent\Concerns\Hashable;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;

class BackendUserController extends Controller
{
    protected bool $paginateListing = true;

    protected function entryType(): EntryType
    {
        return BackendUserEntryType::newInstance();
    }

    public function index(Request $request)
    {
        $this->authorize('list', $this->entryType()::model());

        $paginator = $this->queryListing();

        // Redirect to the first pagination page if the requested page
        // is bigger than the maximum existent page in the paginator.
        if ($paginator->currentPage() > $paginator->lastPage()) {
            return redirect()->route(
                $this->entryType()::routes()->get('index')->routeName(),
                Collection::make($request->query())->except(['page'])->all()
            );
        }

        // Transform the paginator collection, which is composed of
        // entry instances, into a collection of entry resources ready
        // to be transformed to our front-end application.
        $collection = EntryTypeResourceCollection::make(
            $this->entryType(),
            $paginator->items()
        )->withPaginator($paginator)->withMeta([
            'order' => $request->input('order'),
        ]);

        return view('fastlane::backend-users.index', [
            'items' => $collection->toArray(),
        ]);
    }

    protected function queryListing()
    {
        if (Gate::getPolicyFor($this->entryType()::model())) {
            Gate::authorize('index', $this->entryType()::model());
        }

        // Start the query, selecting only fields we really need.
        $defaultColumns = ['id'];

        if (in_array(Hashable::class, class_uses_recursive($this->entryType()::model()))) {
            $defaultColumns[] = 'hashid';
        }

        $columns = $this->entryType()::newInstance()->getFields()->onListing()
            ->getAttributes()->filter(fn(Field $field) => ! $field->isComputed())
            ->keys()->all();

        $query = $this->entryType()::query()
            ->select(array_merge(
                $defaultColumns,
                $columns,
            ));

        return $this->paginateListing ? $query->paginate() : $query->get();
    }
}
