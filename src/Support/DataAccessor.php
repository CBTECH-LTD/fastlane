<?php

namespace CbtechLtd\Fastlane\Support;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;

/**
 * Class DataAccessor
 *
 * This class provides a object-like way to recursively access
 * data of an array. If the given array contains other arrays,
 * these will be wrapped in a DataAccessor instance, that way
 * we can access all properties recursively.
 *
 * For the given array:
 * [
 *      'user' => [
 *          'profile' => [
 *              'name' => 'John Doe',
 *              'roles' => [
 *                  ['name' => 'Administrator', 'id' => 'admin']
 *               ]
 *          ]
 *      ]
 * ]
 *
 * We can get the name value using different methods:
 *
 * $instance->user->profile->name
 * $instance->get('user.profile.name')
 * $instance->get('user.profile')->name
 * $instance->user->roles->get(0)->administrator
 * $instance->user->roles[0]->administrator
 * $instance->get('user.roles.0.name')
 *
 * @package CbtechLtd\Fastlane\Http\ViewModels
 */
class DataAccessor implements \ArrayAccess, Arrayable
{
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function has(string $key): bool
    {
        return Arr::has($this->data, $key);
    }

    public function get(string $key, $default = null)
    {
        return once(function () use ($key, $default) {
            $value = data_get($this->data, $key, $default);

            // Return an instance of this class with the value
            // we just found if it's an array. Otherwise we
            // just return the value itself.
            // We could recursively convert it when this class is
            // instantiated, but we would be wasting some bits because
            // we don't know if the data will be actually used.
            return is_array($value)
                ? new static($value)
                : $value;
        });
    }

    public function toArray()
    {
        return $this->data;
    }

    public function __get($name)
    {
        if (Arr::has($this->data, $name)) {
            return $this->get($name);
        }

        throw new \Exception('No data found for $' . $name);
    }

    public function offsetExists($offset)
    {
        return Arr::has($this->data, $offset);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        //
    }

    public function offsetUnset($offset)
    {
        //
    }
}
