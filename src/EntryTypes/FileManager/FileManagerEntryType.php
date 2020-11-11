<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\EntryTypes\FileManager;

use CbtechLtd\Fastlane\EntryTypes\EntryType;
use CbtechLtd\Fastlane\Fields\Types\ActiveToggle;
use CbtechLtd\Fastlane\Fields\Types\Hidden;
use CbtechLtd\Fastlane\Fields\Types\Panel;
use CbtechLtd\Fastlane\Fields\Types\ShortText;

class FileManagerEntryType extends EntryType
{
    const PERM_MANAGE_FILES = 'manage files';

    /** @var string */
    protected static string $controller = FileManagerEntryType::class;

    /** @var string */
    protected static string $repository = FileManagerRepository::class;

    protected static string $key = 'files';

    protected static ?string $icon = 'image';

    protected static array $label = [
        'singular' => 'File',
        'plural'   => 'Files',
    ];

    /**
     * @inheritDoc
     */
    public static function fields(): array
    {
        return [
            ShortText::make('Name')->required()->sortable()->listable(),
            ShortText::make('Extension')->required()->sortable()->listable()->hideOnForm(),
            Hidden::make('Size')->listable(),
            Hidden::make('Mime Type', 'mimetype')->listable(),
            Hidden::make('URL')->computed()->listable(),
            ShortText::make('File')->required()->listable(),
            Panel::make('Settings')->withIcon('tools')->withFields([
                ActiveToggle::make(),
            ]),
        ];
    }

    protected static function menuGroup(): string
    {
        return __('fastlane::core.menu.system_group');
    }
}
