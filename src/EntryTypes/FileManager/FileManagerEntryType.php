<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\EntryTypes\FileManager;

use CbtechLtd\Fastlane\EntryTypes\EntryType;
use CbtechLtd\Fastlane\FileAttachment\DraftAttachment;
use CbtechLtd\Fastlane\Support\Concerns\RendersOnMenu;
use CbtechLtd\Fastlane\Support\Contracts\EntryInstance;
use CbtechLtd\Fastlane\Support\Contracts\RenderableOnMenu;
use CbtechLtd\Fastlane\Support\Contracts\WithCollectionLinks;
use CbtechLtd\Fastlane\Support\Contracts\WithCustomController;
use CbtechLtd\Fastlane\Support\Contracts\WithCustomViews;
use CbtechLtd\Fastlane\Support\Schema\Fields\FieldPanel;
use CbtechLtd\Fastlane\Support\Schema\Fields\FieldValue;
use CbtechLtd\Fastlane\Support\Schema\Fields\FileField;
use CbtechLtd\Fastlane\Support\Schema\Fields\HiddenField;
use CbtechLtd\Fastlane\Support\Schema\Fields\StringField;
use CbtechLtd\Fastlane\Support\Schema\Fields\ToggleField;
use CbtechLtd\JsonApiTransformer\ApiResources\ResourceLink;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\URL;

class FileManagerEntryType extends EntryType implements RenderableOnMenu, WithCustomViews, WithCollectionLinks, WithCustomController
{
    use RendersOnMenu;

    const PERM_MANAGE_FILES = 'manage files';

    public function identifier(): string
    {
        return 'files';
    }

    public function model(): string
    {
        return File::class;
    }

    public function name(): string
    {
        return __('File');
    }

    public function pluralName(): string
    {
        return __('Files');
    }

    protected function menuGroup(): string
    {
        return __('System');
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
        Collection::make($request->input('file'))->each(function (string $file) use ($request) {
            $draft = DraftAttachment::where('draft_id', $request->input('file__draft_id'))
                ->where('file', $file)
                ->first();

            $req = Request::createFrom($request)->merge([
                'file' => [$file],
                'name' => $draft->name,
            ]);

            $this->store($req);
        });
    }
}
