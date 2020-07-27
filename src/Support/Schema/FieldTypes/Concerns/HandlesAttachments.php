<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields\Concerns;

use Closure;

trait HandlesAttachments
{
    /** @var Closure */
    protected $addFileCallback;

    /** @var Closure */
    protected $removeFileCallback;

    /** @var Closure */
    protected $purgeDraftFilesCallback;

    /** @var string | Closure */
    protected $storageDirectory = 'attachments';

    public function addFile($callback): self
    {
        $this->addFileCallback = $callback;
        return $this;
    }

    public function removeFile($callback): self
    {
        $this->removeFileCallback = $callback;
        return $this;
    }

    public function purgeDraftFiles($callback): self
    {
        $this->purgeDraftFilesCallback = $callback;
        return $this;
    }

    public function getAddFileCallback()
    {
        return $this->addFileCallback;
    }

    public function getRemoveFileCallback()
    {
        return $this->removeFileCallback;
    }

    public function getPurgeDraftFilesCallback()
    {
        return $this->purgeDraftFilesCallback;
    }

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
