<?php

namespace CbtechLtd\Fastlane\Http\Requests;

use CbtechLtd\Fastlane\EntryTypes\BackendUser\BackendUserEntryType;

class UpdateBackendUserRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->hasPermissionTo(BackendUserEntryType::PERM_MANAGE_SYSTEM_ADMINS);
    }

    public function rules()
    {
        return [
            'name'      => 'sometimes|required|string|max:255',
            'email'     => 'sometimes|required|email|max:255|unique:users,email,' . $this->user()->getKey(),
            'password'  => 'sometimes|required|confirmed',
            'is_active' => 'sometimes|required|boolean',
            'role'      => 'sometimes|required|exists:roles,name',
        ];
    }
}
