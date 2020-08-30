<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields\Concerns;

use CbtechLtd\Fastlane\FileAttachment\Contracts\DraftAttachmentHandler;
use CbtechLtd\Fastlane\FileAttachment\DraftAttachment;
use CbtechLtd\Fastlane\FileAttachment\StoreDraftAttachment;
use CbtechLtd\Fastlane\Http\Requests\EntryAttachmentStoreRequest;
use Closure;

trait HandlesAttachments
{
    /** @var Closure */
    protected $addFileHandler;

    /** @var bool | Closure */
    protected $acceptFiles = true;

    /** @var string | Closure */
    protected $storageDirectory = 'attachments';

    public function getStorageDirectory(): string
    {
        if (is_callable($this->storageDirectory)) {
            return call_user_func($this->storageDirectory, $this);
        }

        return $this->storageDirectory;
    }

    public function storeAttachment(EntryAttachmentStoreRequest $request): DraftAttachment
    {
        $callback = is_callable($this->addFileHandler)
            ? $this->addFileHandler
            : new StoreDraftAttachment($this);

        return $callback->handle($request);
    }

    /**
     * @param string | Closure $directory
     * @return $this
     */
    public function storeFilesAt($directory): self
    {
        $this->storageDirectory = $directory;
        return $this;
    }

    public function storeAttachmentUsing(DraftAttachmentHandler $handler): self
    {
        $this->addFileHandler = $handler;
        return $this;
    }

    public function isAcceptingAttachments(): bool
    {
        if (is_callable($this->acceptFiles)) {
            return call_user_func($this->acceptFiles);
        }

        return $this->acceptFiles;
    }

    public function acceptAttachments($accept = true): self
    {
        $this->acceptFiles = $accept;
        return $this;
    }
}
