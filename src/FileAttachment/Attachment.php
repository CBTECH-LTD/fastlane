<?php

namespace CbtechLtd\Fastlane\FileAttachment;

use Altek\Accountant\Contracts\Recordable;
use Altek\Accountant\Recordable as RecordableTrait;
use Altek\Eventually\Eventually;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model implements Recordable
{
    use Eventually, RecordableTrait;

    protected $table = 'fastlane_attachments';

    protected $fillable = [
        'attachable_type',
        'attachable_id',
        'file',
        'url',
        'name',
    ];
}
