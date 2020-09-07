<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\FileAttachment\Contracts;

use CbtechLtd\Fastlane\FileAttachment\AttachmentValue;
use CbtechLtd\Fastlane\Support\Contracts\EntryInstance;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

interface PersistentAttachmentHandler
{
    /**
     * @param EntryInstance $entryInstance
     * @param array         $files
     * @return Collection<AttachmentValue>
     */
    public function read(EntryInstance $entryInstance, array $files): Collection;

    /**
     * @param EntryInstance $entryInstance
     * @param UploadedFile  $file
     * @param string        $directory
     * @return string
     */
    public function write(EntryInstance $entryInstance, UploadedFile $file, string $directory = 'files'): string;

    /**
     * @param EntryInstance $entryInstance
     * @param string        $fieldName
     * @param array         $value
     * @param string        $draftId
     */
    public function remove(EntryInstance $entryInstance, string $fieldName, array $value, string $draftId): void;
}
