<?php

namespace CbtechLtd\Fastlane\EntryTypes\FileManager;

use Altek\Accountant\Contracts\Recordable as RecordableContract;
use Altek\Accountant\Recordable;
use Altek\Eventually\Eventually;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class File extends Model implements RecordableContract
{
    use Eventually, Recordable;

    protected $table = 'fastlane_files';

    protected $fillable = [
        'file',
        'name',
        'extension',
        'size',
        'mimetype',
    ];

    public function url(): string
    {
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
        ];
    }
}
