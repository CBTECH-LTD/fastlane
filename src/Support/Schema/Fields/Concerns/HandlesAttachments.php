<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields\Concerns;

use CbtechLtd\Fastlane\FileAttachment\AttachmentValue;
use CbtechLtd\Fastlane\FileAttachment\Contracts\DraftAttachmentHandler;
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

    public function storeAttachment(EntryAttachmentStoreRequest $request): AttachmentValue
    {
        $defaultHandler = config('fastlane.attachments.draft_handler');

        $callback = $this->addFileHandler instanceof DraftAttachmentHandler
            ? $this->addFileHandler
            : new $defaultHandler($this);

        return $callback->write($request, $this);
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
