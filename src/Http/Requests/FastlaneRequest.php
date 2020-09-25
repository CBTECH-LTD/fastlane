<?php

namespace CbtechLtd\Fastlane\Http\Requests;

use CbtechLtd\Fastlane\Contracts\EntryType as EntryTypeContract;
use CbtechLtd\Fastlane\Facades\EntryType as EntryTypeFacade;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

abstract class FastlaneRequest extends FormRequest
{
    protected EntryTypeContract $entryType;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    abstract public function rules();

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    abstract public function authorize(): bool;

    /**
     * Make an instance of the entry type.
     *
     * @param EntryTypeContract $class
     * @return EntryTypeContract
     */
    abstract protected function makeEntryType($class): EntryTypeContract;

    /**
     * Get the requested entry type.
     *
     * @return EntryTypeContract
     */
    public function entryType(): EntryTypeContract
    {
        return $this->entryType;
    }

    /**
     * Load the requested entry type and its instance.
     */
    protected function prepareForValidation()
    {
        $this->entryType = $this->makeEntryType($this->findEntryTypeClass());
    }

    /**
     * @return EntryTypeContract|string
     */
    protected function findEntryTypeClass(): string
    {
        if (! preg_match('/^fastlane\.cp\.(.+)\.(.+)$/', $this->route()->getName(), $matches)) {
            abort(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return EntryTypeFacade::findByKey($matches[1]);
    }
}
