<?php

namespace CbtechLtd\Fastlane\FileAttachment;

use CbtechLtd\Fastlane\Http\Requests\EntryAttachmentStoreRequest;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\HasAttachments;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;

class DraftAttachmentHandler implements Contracts\DraftAttachmentHandler
{
    /**
     * @param string     $draftId
     * @param array|null $files
     * @return Collection<AttachmentValue>
     */
    public function findDrafts(string $draftId, ?array $files = null): Collection
    {
        return DraftAttachment::query()
            ->where('draft_id', $draftId)
            ->when(is_array($files), function ($q) use ($files) {
                $q->whereIn('file', Collection::make($files)->pluck('file')->all());
            })
            ->get()->map(
                fn(DraftAttachment $a) => (new AttachmentValue(
                    $a->file,
                    $a->name,
                    $a->url(),
                    $a->extension,
                    $a->size,
                    $a->mimetype,
                ))->setAsDraft()
            );
    }

    /**
     * @param EntryAttachmentStoreRequest $request
     * @param HasAttachments              $field
     * @return AttachmentValue
     */
    public function write(EntryAttachmentStoreRequest $request, HasAttachments $field): AttachmentValue
    {
        $disk = Config::get('fastlane.attachments.disk');
        $data = $request->validated();

        /** @var UploadedFile $file */
        $file = $data['files'][0];

        $draft = DraftAttachment::create([
            'handler'   => get_class($this),
            'draft_id'  => $data['draft_id'],
            'name'      => $data['name'],
            'file'      => $file->store($field->getStorageDirectory(), $disk),
            'extension' => $file->extension(),
            'size'      => $file->getSize(),
            'mimetype'  => $file->getMimeType(),
        ]);

        if (! $draft) {
            abort(500);
        }

        return new AttachmentValue(
            $draft->file,
            $draft->name,
            $draft->url(),
            $draft->extension,
            $draft->size,
            $draft->mimetype,
        );
    }
}
