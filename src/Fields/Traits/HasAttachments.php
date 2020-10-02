<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Fields\Traits;

trait HasAttachments
{
    /** @var string */
    protected string $storageDirectory = 'attachments';

    public function getStorageDirectory(): string
    {
        if (is_callable($this->storageDirectory)) {
            return call_user_func($this->storageDirectory, $this);
        }

        return $this->storageDirectory;
    }

    public function getAcceptableMimetypes(): array
    {
        return [];
    }

    /**
     * @param string $directory
     * @return $this
     */
    public function storeFilesAt(string $directory): self
    {
        $this->storageDirectory = $directory;
        return $this;
    }
}
