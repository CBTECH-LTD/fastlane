namespace {{ $namespace }};

use CbtechLtd\Fastlane\Support\Contracts\ModelPolicy;
use Illuminate\Database\Eloquent\Model;

class {{ $class }} implements ModelPolicy
{
    public function before($user, $ability)
    {
        //
    }

    public function list($user)
    {
        return false;
    }

    public function create($user)
    {
        return false;
    }

    public function update($user, Model $model)
    {
        return false;
    }

    public function delete($user, Model $model)
    {
        return false;
    }
}
