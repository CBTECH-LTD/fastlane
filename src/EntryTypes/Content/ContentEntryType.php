<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\EntryTypes\Content;

use CbtechLtd\Fastlane\Contracts\RenderableOnMenu;
use CbtechLtd\Fastlane\EntryTypes\EntryType;
use CbtechLtd\Fastlane\EntryTypes\RendersOnMenu;
use CbtechLtd\Fastlane\Fields\Types\ActiveToggle;
use CbtechLtd\Fastlane\Fields\Types\BlockEditor;
use CbtechLtd\Fastlane\Fields\Types\Panel;
use CbtechLtd\Fastlane\Fields\Types\ShortText;
use CbtechLtd\Fastlane\Fields\Types\Slug;
use CbtechLtd\Fastlane\Fields\Types\Textarea;
use CbtechLtd\Fastlane\Http\Controllers\ContentController;

class ContentEntryType extends EntryType implements RenderableOnMenu
{
    use RendersOnMenu;

    const PERM_MANAGE_CONTENT = 'manage contents';

    /** @var string|null */
    protected static ?string $icon = 'layer-group';

    /** @var string */
    protected static string $repository = ContentRepository::class;

    /** @var string */
    protected static string $controller = ContentController::class;

    /**
     * @inheritDoc
     */
    public static function key(): string
    {
        return __('fastlane::core.content.key');
    }

    /**
     * @inheritDoc
     */
    public static function label(): array
    {
        return [
            'singular' => __('fastlane::core.content.singular_name'),
            'plural'   => __('fastlane::core.content.plural_name'),
        ];
    }

    /**
     * @inheritDoc
     */
    public static function fields(): array
    {
        return [
            Panel::make('Content')->withFields([
                ShortText::make(__('fastlane::core.fields.name'), 'name')->required()->listable()->sortable(),

                Slug::make(__('fastlane::core.fields.slug'), 'slug')
                    ->generateFromField('name')
                    ->required()
                    ->listable()
                    ->sortable(),

                BlockEditor::make(__('fastlane::core.fields.blocks'), 'blocks'),
            ]),

            Panel::make(__('fastlane::core.settings'))
                ->withFields([
                    ShortText::make(__('fastlane::core.fields.meta_title'), 'meta_title'),
                    Textarea::make(__('fastlane::core.fields.meta_description'), 'meta_description'),
                    ActiveToggle::make()->listable(),
                ])
                ->withIcon('tools'),
        ];
    }
}
