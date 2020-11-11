<?php

namespace CbtechLtd\Fastlane\EntryTypes\FileManager;

use CbtechLtd\Fastlane\Repositories\Repository;
use CbtechLtd\Fastlane\Support\Eloquent\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File as FileFacade;
use Illuminate\Support\Str;

class FileManagerRepository extends Repository
{
    public function __construct(File $model)
    {
        $this->model = $model;
    }

    protected function beforeFetchListing(Builder $query): void
    {
        $query->when(request()->input('filter.types'), function (Builder $q, $types) {
            $q->where(function (Builder $q) use ($types) {
                foreach ($types as $type) {
                    if (Str::endsWith($type, '/*')) {
                        $q->orWhere('mimetype', 'like', Str::replaceLast('/*', '', $type) . '%');
                        continue;
                    }

                    $q->orWhere('mimetype', $type);
                }
            });
        });
    }

    protected function beforeSave(BaseModel $model, array $fields, array $data): void
    {
        $uploadedFile = data_get($data, 'files.0');
        $filePath = $uploadedFile->store('files', Config::get('fastlane.attachments.disk'));

        $model->fill([
            'file'      => $filePath,
            'extension' => FileFacade::extension($filePath),
            'mimetype'  => $uploadedFile->getMimeType(),
            'size'      => (string)$uploadedFile->getSize(),
            'active'    => true,
        ]);
    }
}
