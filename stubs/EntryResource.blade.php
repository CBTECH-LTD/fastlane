namespace {{ $namespace }};

use CbtechLtd\JsonApiTransformer\ApiResources\ResourceLink;
use CbtechLtd\JsonApiTransformer\ApiResources\ResourceType;
use Illuminate\Http\Request;

class {{ $class }} extends ResourceType
{
        public function type(): string
        {
            return '{{ $type }}';
        }

        public function attributes(Request $request): array
        {
            return $this->getAttributesFromFillable();
        }

        protected function links(): array
        {
            return [
                ResourceLink::make('self', ['cp.{{ $routeName }}.edit', $this->model]),
                ResourceLink::make('parent', ['cp.{{ $routeName }}.index']),
            ];
        }
}
