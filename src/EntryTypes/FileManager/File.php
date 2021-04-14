<?php

namespace CbtechLtd\Fastlane\EntryTypes\FileManager;

use CbtechLtd\Fastlane\Support\Eloquent\BaseModel;
use Illuminate\Support\Facades\Storage;

class File extends BaseModel
{
    protected $table = 'fastlane_files';

    protected $fillable = [
        'file',
        'name',
        'extension',
        'size',
        'mimetype',
        'parent_id',
    ];

    public function getRouteKeyName()
    {
        return 'id';
    }

    public function isDirectory(): bool
    {
        return $this->mimetype === 'fastlane/directory';
    }

    public function url(): string
    {
        if ($this->isDirectory()) {
            return '';
        }

        return Storage::disk(config('fastlane.attachments.disk'))->url($this->file);
    }

    public function toArray()
    {
        return [
            'id'        => $this->getKey(),
            'file'      => $this->file,
            'name'      => $this->name,
            'url'       => $this->url(),
            'extension' => $this->extension,
            'size'      => $this->size,
            'mimetype'  => $this->mimetype,
            'parent_id' => $this->parent_id,
        ];
    }
}
