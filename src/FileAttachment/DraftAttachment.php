<?php

namespace CbtechLtd\Fastlane\FileAttachment;

use Altek\Accountant\Contracts\Recordable;
use Altek\Accountant\Recordable as RecordableTrait;
use Altek\Eventually\Eventually;
use CbtechLtd\Fastlane\Support\Contracts\SchemaFieldType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DraftAttachment extends Model implements Recordable
{
    use Eventually, RecordableTrait;

    protected $table = 'fastlane_draft_attachments';

    protected $fillable = [
        'draft_id',
        'file',
    ];

    public static function persistAllDraft(string $draftId, SchemaFieldType $field, Model $model): void
    {
        static::where('draft_id', $draftId)
            ->get()
            ->each
            ->persist($field, $model);
    }

    public function persist(SchemaFieldType $field, $model): void
    {
        Attachment::create([
            'attachable_type' => get_class($model),
            'attachable_id'   => $model->getKey(),
            'file'            => $this->file,
            'url'             => Storage::disk(config('fastlane.attachment_disk'))->url($this->file),
        ]);

        $this->delete();
    }
}
