<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields\Concerns;

use CbtechLtd\Fastlane\FileAttachment\Attachment;
use CbtechLtd\Fastlane\FileAttachment\StoreDraftAttachment;
use Closure;
use Illuminate\Http\Request;

trait HandlesAttachments
{
    /** @var Closure */
    protected $addFileCallback;

    /** @var Closure */
    protected $removeFileCallback;

    /** @var Closure */
    protected $purgeDraftFilesCallback;

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

    public function storeAttachment(Request $request): string
    {
        $callback = is_callable($this->addFileCallback)
            ? $this->addFileCallback
            : new StoreDraftAttachment($this);

        return call_user_func($callback, $request);
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

    public function storeAttachmentUsing($callback): self
    {
        $this->addFileCallback = $callback;
        return $this;
    }

    public function removeFileUsing($callback): self
    {
        $this->removeFileCallback = $callback;
        return $this;
    }

    public function purgeDraftFilesUsing($callback): self
    {
        $this->purgeDraftFilesCallback = $callback;
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
