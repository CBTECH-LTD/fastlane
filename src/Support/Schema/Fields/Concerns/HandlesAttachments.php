<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields\Concerns;

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

    /**
     * @param string | Closure $directory
     * @return $this
     */
    public function storeFilesAt($directory): self
    {
        $this->storageDirectory = $directory;
        return $this;
    }
}
