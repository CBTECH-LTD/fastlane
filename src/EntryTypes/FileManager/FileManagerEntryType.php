<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\EntryTypes\FileManager;

use CbtechLtd\Fastlane\EntryTypes\EntryType;
use CbtechLtd\Fastlane\FileAttachment\AttachmentValue;
use CbtechLtd\Fastlane\FileAttachment\Contracts\DraftAttachmentHandler;
use CbtechLtd\Fastlane\Support\Concerns\RendersOnMenu;
use CbtechLtd\Fastlane\Support\Contracts\RenderableOnMenu;
use CbtechLtd\Fastlane\Support\Contracts\WithCollectionLinks;
use CbtechLtd\Fastlane\Support\Contracts\WithCustomController;
use CbtechLtd\Fastlane\Support\Contracts\WithCustomViews;
use CbtechLtd\Fastlane\Support\Schema\Fields\FieldPanel;
use CbtechLtd\Fastlane\Support\Schema\Fields\FileField;
use CbtechLtd\Fastlane\Support\Schema\Fields\StringField;
use CbtechLtd\Fastlane\Support\Schema\Fields\ToggleField;
use CbtechLtd\JsonApiTransformer\ApiResources\ResourceLink;
use Illuminate\Http\Request;

class FileManagerEntryType extends EntryType implements RenderableOnMenu, WithCustomViews, WithCollectionLinks, WithCustomController
{
    use RendersOnMenu;

    const PERM_MANAGE_FILES = 'manage files';

    public function identifier(): string
    {
        return __('fastlane::core.file_manager.identifier');
    }

    public function model(): string
    {
        return File::class;
    }

    public function name(): string
    {
        return __('fastlane::core.file_manager.singular_name');
    }

    public function pluralName(): string
    {
        return __('fastlane::core.file_manager.plural_name');
    }

    protected function menuGroup(): string
    {
        return __('fastlane::core.menu.system_group');
    }

    public function icon(): string
    {
        return 'image';
    }

    public function fields(): array
    {
        return [
            StringField::make('name', 'Name')
                ->required()
                ->showOnIndex()
                ->sortable(),

            FileField::make('file', 'File')
                ->withDescription(__('Select the files you want to upload. You can select as many files as you want.'))
                ->showOnIndex()
                ->required(),

            FieldPanel::make('Settings')->withIcon('tools')
                ->withFields([
                    ToggleField::make('is_active', 'Active')
                        ->required()
                        ->showOnIndex(),
                ]),
        ];
    }

    public function getIndexView(): ?string
    {
        return 'FileManager/Index';
    }

    public function getCreateView(): ?string
    {
        return null;
    }

    public function getEditView(): ?string
    {
        return null;
    }

    public function collectionLinks(): array
    {
        return [
            ResourceLink::make('upload', ["cp.{$this->identifier()}.store"]),
        ];
    }

    public function getController(): string
    {
        return FileManagerController::class;
    }

    public function storeMany(Request $request): void
    {
        /** @var DraftAttachmentHandler $handler */
        $handler = app()->make(config('fastlane.attachments.draft_handler'));

        $handler->findDrafts($request->input('file__draft_id'), $request->input('file'))
            ->each(function (AttachmentValue $value) use ($request) {
                $req = Request::createFrom($request)->merge([
                    'file' => [$value->getFile()],
                    'name' => $value->getName(),
                ]);

                $this->store($req);
            });
    }
}
