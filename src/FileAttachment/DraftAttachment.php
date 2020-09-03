<?php

namespace CbtechLtd\Fastlane\FileAttachment;

use Altek\Accountant\Contracts\Recordable;
use Altek\Accountant\Recordable as RecordableTrait;
use Altek\Eventually\Eventually;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DraftAttachment extends Model implements Recordable
{
    use Eventually, RecordableTrait;

    protected $table = 'fastlane_draft_attachments';

    protected $fillable = [
        'draft_id',
        'file',
        'name',
        'extension',
        'size',
        'mimetype',
        'handler',
    ];

    public function url(): string
    {
        return Storage::disk(config('fastlane.attachments.disk'))->url($this->file);
    }
}
