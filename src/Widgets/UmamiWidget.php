<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Widgets;

use Illuminate\Contracts\Support\Arrayable;

class UmamiWidget implements Arrayable
{
    protected string $id = 'umami';
    protected string $label = 'Analytics';
    protected string $component = 'umami-widget';
    protected ?string $url;

    public static function make(string $url): UmamiWidget
    {
        return new static($url);
    }

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function getComponent(): string
    {
        return $this->component;
    }

    public function toArray()
    {
        return [
            'id'        => $this->getId(),
            'label'     => $this->getLabel(),
            'component' => $this->getComponent(),
            'data'      => [
                'url' => $this->url,
            ],
        ];
    }
}
