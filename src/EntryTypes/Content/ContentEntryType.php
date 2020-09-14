<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\EntryTypes\Content;

use CbtechLtd\Fastlane\EntryTypes\EntryType;
use CbtechLtd\Fastlane\Support\Concerns\RendersOnMenu;
use CbtechLtd\Fastlane\Support\Contracts\RenderableOnMenu;
use CbtechLtd\Fastlane\Support\Schema\Fields\BlockEditorField;
use CbtechLtd\Fastlane\Support\Schema\Fields\FieldPanel;
use CbtechLtd\Fastlane\Support\Schema\Fields\SlugField;
use CbtechLtd\Fastlane\Support\Schema\Fields\StringField;
use CbtechLtd\Fastlane\Support\Schema\Fields\TextField;
use CbtechLtd\Fastlane\Support\Schema\Fields\ToggleField;

class ContentEntryType extends EntryType implements RenderableOnMenu
{
    use RendersOnMenu;

    const PERM_MANAGE_CONTENT = 'manage contents';

    public function identifier(): string
    {
        return __('fastlane::core.content.identifier');
    }

    public function model(): string
    {
        return Content::class;
    }

    public function name(): string
    {
        return __('fastlane::core.content.singular_name');
    }

    public function pluralName(): string
    {
        return __('fastlane::core.content.plural_name');
    }

    public function icon(): string
    {
        return 'layer-group';
    }

    public function fields(): array
    {
        return [
            StringField::make('name', __('fastlane::core.fields.name'))
                ->required()
                ->showOnIndex()
                ->sortable(),

            SlugField::make('slug', __('fastlane::core.fields.slug'))
                ->makeFromField('name')
                ->required()
                ->showOnIndex()
                ->sortable(),

            BlockEditorField::make('blocks', __('fastlane::core.fields.blocks')),

            FieldPanel::make(__('fastlane::core.settings'))->withIcon('tools')
                ->withFields([
                    StringField::make('meta_title', __('fastlane::core.fields.meta_title')),
                    TextField::make('meta_description', __('fastlane::core.fields.meta_description')),

                    ToggleField::make('is_active', __('fastlane::core.fields.active'))
                        ->required()
                        ->showOnIndex(),
                ]),
        ];
    }

    public function queryBuilder(): QueryBuilder
    {
        return new QueryBuilder($this);
    }
}
