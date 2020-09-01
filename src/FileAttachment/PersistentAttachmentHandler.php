<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\FileAttachment;

use CbtechLtd\Fastlane\EntryTypes\EntryType;
use CbtechLtd\Fastlane\EntryTypes\Hooks\OnSavingHook;
use CbtechLtd\Fastlane\Support\Contracts\EntryInstance;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PersistentAttachmentHandler implements Contracts\PersistentAttachmentHandler
{
    protected Filesystem $disk;

    public function __construct()
    {
        $this->disk = Storage::disk(Config::get('fastlane.attachments.disk'));
    }

    /**
     * @param EntryInstance $entryInstance
     * @param array         $files
     * @return Collection<AttachmentValue>
     */
    public function read(EntryInstance $entryInstance, array $files): Collection
    {
        return Attachment::query()
            ->whereIn('file', $files)
            ->where('attachable_type', get_class($entryInstance->model()))
            ->where('attachable_id', $entryInstance->model()->getKey())
            ->get()->map(
                fn(Attachment $a) => new AttachmentValue(
                    $a->file,
                    $a->name,
                    $a->url(),
                    $a->extension,
                    $a->size,
                    $a->mimetype,
                )
            );
    }

    /**
     * @param EntryInstance $entryInstance
     * @param string        $fieldName
     * @param array         $value
     * @param string        $draftId
     */
    public function write(EntryInstance $entryInstance, string $fieldName, array $value, string $draftId): void
    {
        $files = Collection::make($value)->pluck('file')->all();
        $entryInstance->model()->{$fieldName} = $files;

        // We persist draft attachments after the model has being saved because
        // wee need the model's id.
        // TODO: Find a way to persist drafts before saving the model to guarantee
        //       that we don't get models with files information while attachments
        //       does not actually exist.
        $entryInstance->type()->addHook(EntryType::HOOK_AFTER_SAVING, function (OnSavingHook $hook) use ($entryInstance, $files, $draftId) {
            DraftAttachment::query()
                ->where('draft_id', $draftId)
                ->whereIn('file', $files)
                ->get()
                ->map(function (DraftAttachment $draft) use ($draftId, $entryInstance) {
                    return $this->persist($entryInstance, $draft);
                });
        });
    }

    protected function persist(EntryInstance $entryInstance, DraftAttachment $draft): AttachmentValue
    {
        $attachment = Attachment::create([
            'attachable_type' => get_class($entryInstance->model()),
            'attachable_id'   => $entryInstance->model()->getKey(),
            'name'            => $draft->name,
            'file'            => $draft->file,
            'extension'       => $draft->extension,
            'size'            => $draft->size,
            'mimetype'        => $draft->mimetype,
            'url'             => $draft->url(),
            'handler'         => $draft->handler,
        ]);

        $draft->delete();

        return new AttachmentValue(
            $attachment->file,
            $attachment->name,
            $attachment->url(),
            $attachment->extension,
            $attachment->size,
            $attachment->mimetype,
        );
    }

    /**
     * @param EntryInstance $entryInstance
     * @param string        $fieldName
     * @param array         $value
     * @param string        $draftId
     */
    public function remove(EntryInstance $entryInstance, string $fieldName, array $value, string $draftId): void
    {
        Attachment::query()
            ->where('attachable_type', get_class($entryInstance->model()))
            ->where('attachable_id', $entryInstance->model()->getKey())
            ->whereIn('file', Collection::make($value)->pluck('file')->all())
            ->get()
            ->each(function (Attachment $attachment) {
                Log::info("FASTLANE :: Attachment {$attachment->file} removed.");
                $this->disk->delete($attachment->file);
                $attachment->forceDelete();
            });
    }
}
