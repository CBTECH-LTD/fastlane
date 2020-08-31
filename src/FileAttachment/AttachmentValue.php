<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\FileAttachment;

use Illuminate\Contracts\Support\Arrayable;

class AttachmentValue implements Arrayable
{
    private string $file;
    private string $name;
    private string $url;

    public function __construct(string $file, string $name, string $url)
    {
        $this->file = $file;
        $this->name = $name;
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getFile(): string
    {
        return $this->file;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    public function toArray()
    {
        return [
            'file' => $this->file,
            'name' => $this->name,
            'url'  => $this->url,
        ];
    }
}
